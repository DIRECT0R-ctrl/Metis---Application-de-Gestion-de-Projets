<?php

class ProjetCourt extends Projet
{
    private int $duree;

    public function __construct(string $titre, int $membreId, int $duree)
    {
        parent::__construct($titre, $membreId);
        $this->setDuree($duree);
    }

    public function setDuree(int $duree): void
    {
        if ($duree <= 0) {
            throw new InvalidArgumentException("Durée invalide");
        }
        $this->duree = $duree;
    }

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function getType(): string
    {
        return 'court';
    }
}

