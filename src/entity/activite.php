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

    public function setDescription(string $description): void
    {
        if (empty($description)) {
            throw new InvalidArgumentException("Description vide");
        }
        $this->description = $description;
    }
}

