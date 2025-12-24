<?php

abstract class Projet
{
    protected int $id;
    protected string $titre;
    protected DateTime $dateDebut;
    protected int $membreId;

    public function __construct(string $titre, int $membreId)
    {
        $this->setTitre($titre);
        $this->membreId = $membreId;
        $this->dateDebut = new DateTime();
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): void
    {
        if (empty($titre)) {
            throw new InvalidArgumentException("Le titre ne peut pas être vide");
        }
        $this->titre = $titre;
    }
}

