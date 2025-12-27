<?php
require_once __DIR__ . '/database/database.php';

try {
    $pdo = Database::connect();
    echo "✓ Connexion à la base de données réussie!\n";
    echo "✓ PDO fonctionne correctement\n";
    
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "✓ Version PostgreSQL: " . substr($version, 0, 50) . "...\n";
    
} catch (PDOException $e) {
    echo "✗ Erreur de connexion: " . $e->getMessage() . "\n";
    echo "\nPour créer la base de données:\n";
    echo "1. Créez la base: CREATE DATABASE metis;\n";
    echo "2. Créez l'utilisateur: CREATE USER metis_user WITH PASSWORD 'metis123';\n";
    echo "3. Donnez les droits: GRANT ALL PRIVILEGES ON DATABASE metis TO metis_user;\n";
    echo "4. Exécutez le schema: psql -U metis_user -d metis -f database/schema.sql\n";
}

