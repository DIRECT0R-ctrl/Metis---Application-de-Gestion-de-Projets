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

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDateDebut(): DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(DateTime $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function getMembreId(): int
    {
        return $this->membreId;
    }

    public function setMembreId(int $membreId): void
    {
        $this->membreId = $membreId;
    }

    abstract public function getType(): string;
}
