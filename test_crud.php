<?php
require_once __DIR__ . '/database/database.php';
require_once __DIR__ . '/src/repository/memberRepository.php';
require_once __DIR__ . '/src/repository/projectRepository.php';
require_once __DIR__ . '/src/repository/activiteRepository.php';
require_once __DIR__ . '/src/entity/member.php';
require_once __DIR__ . '/src/entity/projet.php';
require_once __DIR__ . '/src/entity/projetCourt.php';
require_once __DIR__ . '/src/entity/projetLong.php';
require_once __DIR__ . '/src/entity/activite.php';

echo "=== TEST CRUD METIS ===\n\n";

$memberRepo = new MemberRepository();
$projectRepo = new ProjectRepository();
$activiteRepo = new ActiviteRepository();

try {
    echo "1. TEST CRÉATION MEMBRE\n";
    echo "   Création d'un membre de test...\n";
    $testMember = new Member("Test User", "test@example.com");
    if (!$memberRepo->emailExists("test@example.com")) {
        $memberRepo->create($testMember);
        echo "   ✓ Membre créé avec succès\n";
    } else {
        echo "   ⚠ Email déjà existant (test ignoré)\n";
    }
    
    echo "\n2. TEST LECTURE MEMBRES\n";
    $members = $memberRepo->findAll();
    echo "   ✓ Nombre de membres: " . count($members) . "\n";
    if (count($members) > 0) {
        $lastMember = end($members);
        echo "   ✓ Dernier membre: ID={$lastMember['id']}, Nom={$lastMember['nom']}\n";
    }
    
    echo "\n3. TEST CRÉATION PROJET\n";
    if (count($members) > 0) {
        $memberId = $members[0]['id'];
        echo "   Création d'un projet court pour le membre ID=$memberId...\n";
        $projet = new ProjetCourt("Projet Test", $memberId, 30);
        $projectRepo->create($projet);
        echo "   ✓ Projet créé avec succès\n";
    }
    
    echo "\n4. TEST LECTURE PROJETS\n";
    $projets = $projectRepo->findAll();
    echo "   ✓ Nombre de projets: " . count($projets) . "\n";
    if (count($projets) > 0) {
        $lastProjet = end($projets);
        echo "   ✓ Dernier projet: ID={$lastProjet['id']}, Titre={$lastProjet['titre']}, Type={$lastProjet['type']}\n";
    }
    
    echo "\n5. TEST CRÉATION ACTIVITÉ\n";
    if (count($projets) > 0) {
        $projetId = $projets[0]['id'];
        echo "   Création d'une activité pour le projet ID=$projetId...\n";
        $activite = new Activite("Activité de test", $projetId);
        $activiteRepo->create($activite);
        echo "   ✓ Activité créée avec succès\n";
    }
    
    echo "\n6. TEST LECTURE ACTIVITÉS\n";
    $activites = $activiteRepo->findAll();
    echo "   ✓ Nombre d'activités: " . count($activites) . "\n";
    if (count($activites) > 0) {
        $lastActivite = end($activites);
        $statut = isset($lastActivite['statut']) ? $lastActivite['statut'] : 'N/A';
        echo "   ✓ Dernière activité: ID={$lastActivite['id']}, Description={$lastActivite['description']}, Statut=$statut\n";
    }
    
    echo "\n7. TEST VÉRIFICATIONS\n";
    if (count($members) > 0) {
        $memberId = $members[0]['id'];
        $hasProjects = $memberRepo->hasProjects($memberId);
        echo "   ✓ Le membre ID=$memberId a des projets: " . ($hasProjects ? "Oui" : "Non") . "\n";
    }
    
    if (count($projets) > 0) {
        $projetId = $projets[0]['id'];
        $hasActive = $projectRepo->hasActiveActivities($projetId);
        echo "   ✓ Le projet ID=$projetId a des activités en cours: " . ($hasActive ? "Oui" : "Non") . "\n";
    }
    
    echo "\n=== TOUS LES TESTS SONT PASSÉS ===\n";
    echo "✓ L'application fonctionne correctement!\n";
    
} catch (Exception $e) {
    echo "\n✗ ERREUR: " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . "\n";
    echo "   Ligne: " . $e->getLine() . "\n";
}

