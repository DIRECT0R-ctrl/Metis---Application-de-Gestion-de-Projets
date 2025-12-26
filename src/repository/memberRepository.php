<?php

require_once __DIR__ . '/../entity/Member.php';
require_once __DIR__ . '/../../database/Database.php';

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
}
