<?php

class ProjetLong extends Projet
{
    private float $budget;

    public function __construct(string $titre, int $membreId, float $budget)
    {
        parent::__construct($titre, $membreId);
        $this->setBudget($budget);
    }

    public function setBudget(float $budget): void
    {
        if ($budget <= 0) {
            throw new InvalidArgumentException("Budget invalide");
        }
        $this->budget = $budget;
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function getType(): string
    {
        return 'long';
    }
}

