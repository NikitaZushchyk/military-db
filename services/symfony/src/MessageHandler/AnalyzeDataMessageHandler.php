<?php

namespace App\MessageHandler;

use App\Entity\AnalysisResult;
use App\Message\AnalyzeDataMessage;
use Doctrine\ORM\EntityManagerInterface;
use Elastic\Elasticsearch\Client;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AnalyzeDataMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Client $elasticsearchClient
    ) {}

    public function __invoke(AnalyzeDataMessage $message): void
    {
        $soldierId = $message->soldierId;
        $stats = $message->stats;

        // *todo Add include a go microservice for hard work
        $score = array_sum($stats) * 1.5;

        $result = new AnalysisResult();
        $result->setSoldierId($soldierId);
        $result->setCalculatedScore($score);
        $result->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($result);
        $this->entityManager->flush();

        try {
            $this->elasticsearchClient->index([
                'index' => 'soldier_analytics',
                'body'  => [
                    'soldier_id' => $soldierId,
                    'score'      => $score,
                    'timestamp'  => $result->getCreatedAt()->format('c')
                ]
            ]);
            echo "Успішно оброблено та збережено аналітику для солдата ID: $soldierId \n";
        } catch (\Exception $e) {
            echo "Помилка відправки в Elasticsearch: " . $e->getMessage() . "\n";
        }
    }
}