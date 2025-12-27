<?php

require_once __DIR__ . '/../entity/projet.php';
require_once __DIR__ . '/../entity/projetCourt.php';
require_once __DIR__ . '/../entity/projetLong.php';
require_once __DIR__ . '/../../database/database.php';

class ProjectRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    private function columnExists(string $table, string $column): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.columns 
             WHERE table_name = :table AND column_name = :column"
        );
        $stmt->execute(['table' => $table, 'column' => $column]);
        return $stmt->fetchColumn() > 0;
    }

    public function create(Projet $projet): void
    {
        $type = $projet->getType();
        $duree = null;
        $budget = null;

        if ($projet instanceof ProjetCourt) {
            $duree = $projet->getDuree();
        } elseif ($projet instanceof ProjetLong) {
            $budget = $projet->getBudget();
        }

        $hasDuree = $this->columnExists('projets', 'duree');
        $hasBudget = $this->columnExists('projets', 'budget');

        $columns = ['titre', 'type', 'date_debut', 'membre_id'];
        $values = [':titre', ':type', ':date', ':membre'];
        $params = [
            'titre' => $projet->getTitre(),
            'type' => $type,
            'date' => $projet->getDateDebut()->format('Y-m-d H:i:s'),
            'membre' => $projet->getMembreId()
        ];

        if ($hasDuree) {
            $columns[] = 'duree';
            $values[] = ':duree';
            $params['duree'] = $duree;
        }

        if ($hasBudget) {
            $columns[] = 'budget';
            $values[] = ':budget';
            $params['budget'] = $budget;
        }

        $sql = "INSERT INTO projets (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $values) . ")";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM projets ORDER BY date_debut DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByMember(int $membreId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM projets WHERE membre_id = :membre_id ORDER BY date_debut DESC"
        );
        $stmt->execute(['membre_id' => $membreId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projets WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function hasActiveActivities(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM activites WHERE projet_id = :id AND statut = 'en cours'"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM projets WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}

