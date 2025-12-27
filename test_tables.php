<?php
require_once __DIR__ . '/database/database.php';

try {
    $pdo = Database::connect();
    
    $tables = ['membres', 'projets', 'activites'];
    echo "Vérification des tables:\n\n";
    
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_name = '$table'");
        $exists = $stmt->fetchColumn() > 0;
        
        if ($exists) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "✓ Table '$table' existe ($count enregistrements)\n";
        } else {
            echo "✗ Table '$table' n'existe pas\n";
        }
    }
    
    echo "\n";
    $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
    $total = $stmt->fetchColumn();
    echo "Total de tables dans la base: $total\n";
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}

