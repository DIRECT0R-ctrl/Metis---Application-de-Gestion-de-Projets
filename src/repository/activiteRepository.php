<?php

require_once __DIR__ . '/../entity/activite.php';
require_once __DIR__ . '/../../database/database.php';

class ActiviteRepository
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

    public function create(Activite $activite): void
    {
        $hasStatut = $this->columnExists('activites', 'statut');
        $dateColumn = $this->columnExists('activites', 'date_activite') ? 'date_activite' : 'date';

        $columns = ['description', 'projet_id'];
        $values = [':description', ':projet_id'];
        $params = [
            'description' => $activite->getDescription(),
            'projet_id' => $activite->getProjetId()
        ];

        if ($hasStatut) {
            $columns[] = 'statut';
            $values[] = ':statut';
            $params['statut'] = $activite->getStatut();
        }

        $columns[] = $dateColumn;
        $values[] = ':date';
        $params['date'] = $activite->getDate()->format('Y-m-d H:i:s');

        $sql = "INSERT INTO activites (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $values) . ")";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    
    public function findAll(): array
    {
        $dateColumn = $this->columnExists('activites', 'date_activite') ? 'date_activite' : 'date';
        $stmt = $this->pdo->query("SELECT * FROM activites ORDER BY $dateColumn DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function findByProject(int $projectId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM activites WHERE projet_id = :projet_id"
        );
        $stmt->execute(['projet_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function updateStatut(int $id, string $statut): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE activites SET statut = :statut WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'statut' => $statut
        ]);
    }

    
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM activites WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
    }


    public function exists(string $description, int $projectId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM activites WHERE description = :description AND projet_id = :projet_id"
        );
        $stmt->execute([
            'description' => $description,
            'projet_id' => $projectId
        ]);
        return $stmt->fetchColumn() > 0;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM activites WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function update(int $id, string $description, string $statut): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE activites SET description = :description, statut = :statut WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'description' => $description,
            'statut' => $statut
        ]);
    }
}