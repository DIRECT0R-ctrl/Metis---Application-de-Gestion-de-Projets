<?php
require_once __DIR__ . '/database/database.php';

try {
    $pdo = Database::connect();
    
    echo "Vérification et mise à jour du schéma...\n\n";
    
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'projets' AND column_name IN ('duree', 'budget')");
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('duree', $existing)) {
        echo "Ajout de la colonne 'duree'...\n";
        $pdo->exec("ALTER TABLE projets ADD COLUMN duree INT");
        echo "✓ Colonne 'duree' ajoutée\n";
    } else {
        echo "✓ Colonne 'duree' existe déjà\n";
    }
    
    if (!in_array('budget', $existing)) {
        echo "Ajout de la colonne 'budget'...\n";
        $pdo->exec("ALTER TABLE projets ADD COLUMN budget DECIMAL(10,2)");
        echo "✓ Colonne 'budget' ajoutée\n";
    } else {
        echo "✓ Colonne 'budget' existe déjà\n";
    }
    
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'activites' AND column_name = 'statut'");
    $hasStatut = $stmt->fetchColumn() !== false;
    
    if (!$hasStatut) {
        echo "Ajout de la colonne 'statut'...\n";
        $pdo->exec("ALTER TABLE activites ADD COLUMN statut VARCHAR(50) NOT NULL DEFAULT 'en cours'");
        echo "✓ Colonne 'statut' ajoutée\n";
    } else {
        echo "✓ Colonne 'statut' existe déjà\n";
    }
    
    echo "\n✓ Schéma mis à jour avec succès!\n";
    
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}

