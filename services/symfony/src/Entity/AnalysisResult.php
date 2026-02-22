<?php

namespace App\Entity;

use App\Repository\AnalysisResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnalysisResultRepository::class)]
class AnalysisResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $soldierId = null;

    #[ORM\Column(nullable: true)]
    private ?float $calculatedScore = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoldierId(): ?int
    {
        return $this->soldierId;
    }

    public function setSoldierId(int $soldierId): static
    {
        $this->soldierId = $soldierId;

        return $this;
    }

    public function getCalculatedScore(): ?float
    {
        return $this->calculatedScore;
    }

    public function setCalculatedScore(?float $calculatedScore): static
    {
        $this->calculatedScore = $calculatedScore;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
