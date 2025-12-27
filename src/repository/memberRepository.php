<?php

require_once __DIR__ . '/../entity/member.php';
require_once __DIR__ . '/../../database/database.php';

class MemberRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    
    public function create(Member $member): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO membres (nom, email, created_at)
             VALUES (:nom, :email, :created_at)"
        );

        $stmt->execute([
            'nom' => $member->getNom(),
            'email' => $member->getEmail(),
            'created_at' => $member->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }
    
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM membres");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM membres
             WHERE id = :id
             AND id NOT IN (SELECT membre_id FROM projets)"
        );

        $stmt->execute(['id' => $id]);
    }


    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM membres WHERE email = :email"
        );

        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM membres WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function update(int $id, string $nom, string $email): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE membres SET nom = :nom, email = :email WHERE id = :id"
        );
        $stmt->execute([
            'id' => $id,
            'nom' => $nom,
            'email' => $email
        ]);
    }

    public function hasProjects(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM projets WHERE membre_id = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}
