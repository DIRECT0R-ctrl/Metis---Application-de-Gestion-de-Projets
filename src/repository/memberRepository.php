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
	
}
