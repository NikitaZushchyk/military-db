<?php

namespace App\Services;

use Pdfgenerator\ReportServiceClient;
use Pdfgenerator\UniversalTableRequest;
use Pdfgenerator\TableRow;
use Grpc\ChannelCredentials;
use Illuminate\Support\Facades\Log;

class PdfExportService
{
    public function generate(string $title, array $headers, array $rowsData): ?string
    {
        $client = new ReportServiceClient('military_go:50051', [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);

        $request = new UniversalTableRequest();
        $request->setTitle($title);

        $request->setHeaders($headers);

        $tableRows = [];
        foreach ($rowsData as $rowData) {
            $row = new TableRow();
            $row->setCells($rowData);
            $tableRows[] = $row;
        }
        $request->setRows($tableRows);

        /** @var \Pdfgenerator\PdfResponse $response */
        list($response, $status) = $client->GenerateTablePdf($request)->wait();

        if ($status->code !== \Grpc\STATUS_OK) {
            Log::error("gRPC Error generating PDF", [
                'code' => $status->code,
                'details' => $status->details
            ]);
            throw new \Exception("Не вдалося згенерувати PDF звіт. Помилка мікросервісу.");
        }

        return $response->getFileData();
    }
}
