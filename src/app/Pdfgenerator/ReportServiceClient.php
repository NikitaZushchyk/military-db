<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Pdfgenerator;

/**
 */
class ReportServiceClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Pdfgenerator\UniversalTableRequest $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     */
    public function GenerateTablePdf(\Pdfgenerator\UniversalTableRequest $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/pdfgenerator.ReportService/GenerateTablePdf',
        $argument,
        ['\Pdfgenerator\PdfResponse', 'decode'],
        $metadata, $options);
    }

}
