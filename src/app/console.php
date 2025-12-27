<?php
require_once __DIR__ . '/../repository/memberRepository.php';
require_once __DIR__ . '/../repository/projectRepository.php';
require_once __DIR__ . '/../repository/activiteRepository.php';
require_once __DIR__ . '/../entity/member.php';
require_once __DIR__ . '/../entity/ProjetCourt.php';
require_once __DIR__ . '/../entity/ProjetLong.php';
require_once __DIR__ . '/../entity/activite.php';


$memberRepo = new MemberRepository();
$projectRepo = new ProjectRepository();
$activiteRepo = new ActiviteRepository();

function printMenu() {
    echo "\n=== METIS - Gestion des Membres & Projets ===\n";
    echo "1. Ajouter un membre\n";
    echo "2. Lister les membres\n";
    echo "3. Ajouter un projet\n";
    echo "4. Lister les projets\n";
    echo "5. Ajouter une activité\n";
    echo "6. Lister les activités\n";
    echo "7. Quitter\n";
    echo "Choix: ";
}

while (true) {
    printMenu();
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case '1': 
            echo "Nom: ";
            $nom = trim(fgets(STDIN));
            echo "Email: ";
            $email = trim(fgets(STDIN));

            if ($memberRepo->emailExists($email)) {
                echo "Erreur: email déjà utilisé.\n";
                break;
            }

            $member = new Member($nom, $email);
            $memberRepo->create($member);
            echo "Membre ajouté avec succès.\n";
            break;

        case '2':
            $members = $memberRepo->findAll();
            if (!$members) {
                echo "Aucun membre trouvé.\n";
            } else {
                foreach ($members as $m) {
                    echo "ID: {$m['id']} | Nom: {$m['nom']} | Email: {$m['email']} | Créé: {$m['created_at']}\n";
                }
            }
            break;

        case '3':
            $members = $memberRepo->findAll();
            if (!$members) {
                echo "Aucun membre disponible. Ajoutez un membre d'abord.\n";
                break;
            }

            echo "Titre du projet: ";
            $titre = trim(fgets(STDIN));

            echo "ID du membre: ";
            $membreId = (int)trim(fgets(STDIN));

            echo "Type (court/long): ";
            $type = strtolower(trim(fgets(STDIN)));

            if ($type === 'court') {
                echo "Durée (jours): ";
                $duree = (int)trim(fgets(STDIN));
                $projet = new ProjetCourt($titre, $membreId, $duree);
            } elseif ($type === 'long') {
                echo "Budget: ";
                $budget = (float)trim(fgets(STDIN));
                $projet = new ProjetLong($titre, $membreId, $budget);
            } else {
                echo "Type invalide.\n";
                break;
            }

            $projectRepo->create($projet);
            echo "Projet ajouté avec succès.\n";
            break;

        case '4': 
            echo "TODO: lister les projets (selon votre table SQL et type)\n";
            break;

        case '5': 
            echo "Description de l'activité: ";
            $desc = trim(fgets(STDIN));
            echo "ID du projet: ";
            $projetId = (int)trim(fgets(STDIN));

            $activite = new Activite($desc, $projetId);
            $activiteRepo->create($activite);
            echo "Activité ajoutée avec succès.\n";
            break;

        case '6': 
            $activities = $activiteRepo->findAll();
            if (!$activities) {
                echo "Aucune activité trouvée.\n";
            } else {
                foreach ($activities as $a) {
                    echo "ID: {$a['id']} | Description: {$a['description']} | Statut: {$a['statut']} | Date: {$a['date']} | Projet ID: {$a['projet_id']}\n";
                }
            }
            break;

        case '7': 
            echo "Au revoir !\n";
            exit;

        default:
            echo "Choix invalide. Veuillez entrer un numéro valide.\n";
            break;
    }
}

?>
