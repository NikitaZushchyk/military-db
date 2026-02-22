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

        $healthScore = match ($stats['status']) {
            'active' => 100,
            'hospital' => 0,
            'vacation' => 80,
            'fired' => 0,
            default => 50,
        };

        $equipmentCompleteness = min(100, $stats['equipment_count'] * 33.3);

        $rankMultiplier = 1.0 + ($stats['rank_id'] * 0.1);

        $fatigue = exp(0.05 * $stats['duty_hours']);

        $score = ($healthScore * ($equipmentCompleteness / 100)) * $rankMultiplier - $fatigue;

        $finalScore = max(0, round($score, 2));

        $result = new AnalysisResult();
        $result->setSoldierId($soldierId);
        $result->setCalculatedScore($finalScore);
        $result->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($result);
        $this->entityManager->flush();

        try {
            $this->elasticsearchClient->index([
                'index' => 'soldier_analytics',
                'body'  => [
                    'soldier_id' => $soldierId,
                    'score'      => $finalScore,
                    'status'     => $stats['status'],
                    'timestamp'  => $result->getCreatedAt()->format('c')
                ]
            ]);
        } catch (\Exception $e) {
            echo "Elasticsearch Error: " . $e->getMessage() . "\n";
        }
    }
}