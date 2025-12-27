<?php

class Activite
{
    private int $id;
    private string $description;
    private string $statut;
    private DateTime $date;
    private int $projetId;

    public function __construct(string $description, int $projetId)
    {
        $this->setDescription($description);
        $this->statut = "en cours";
        $this->date = new DateTime();
        $this->projetId = $projetId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getProjetId(): int
    {
        return $this->projetId;
    }

    public function setDescription(string $description): void
    {
        if (empty($description)) {
            throw new InvalidArgumentException("Description vide");
        }
        $this->description = $description;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function setDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function setProjetId(int $projetId): void
    {
        $this->projetId = $projetId;
    }
}

