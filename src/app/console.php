<?php
require_once __DIR__ . '/../../database/database.php';
require_once __DIR__ . '/../repository/memberRepository.php';
require_once __DIR__ . '/../repository/projectRepository.php';
require_once __DIR__ . '/../repository/activiteRepository.php';
require_once __DIR__ . '/../entity/member.php';
require_once __DIR__ . '/../entity/projet.php';
require_once __DIR__ . '/../entity/projetCourt.php';
require_once __DIR__ . '/../entity/projetLong.php';
require_once __DIR__ . '/../entity/activite.php';

$memberRepo = new MemberRepository();
$projectRepo = new ProjectRepository();
$activiteRepo = new ActiviteRepository();

function printMenu() {
    echo "\n=== METIS - Gestion des Membres & Projets ===\n";
    echo "\n--- MEMBRES ---\n";
    echo "1. Créer un membre\n";
    echo "2. Lister tous les membres\n";
    echo "3. Consulter un membre\n";
    echo "4. Modifier un membre\n";
    echo "5. Supprimer un membre\n";
    echo "\n--- PROJETS ---\n";
    echo "6. Créer un projet\n";
    echo "7. Lister tous les projets\n";
    echo "8. Consulter les projets d'un membre\n";
    echo "9. Supprimer un projet\n";
    echo "\n--- ACTIVITÉS ---\n";
    echo "10. Ajouter une activité\n";
    echo "11. Modifier une activité\n";
    echo "12. Supprimer une activité\n";
    echo "13. Consulter l'historique d'un projet\n";
    echo "\n0. Quitter\n";
    echo "Choix: ";
}

while (true) {
    printMenu();
    $choice = trim(fgets(STDIN));

    try {
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
                echo "Membre créé avec succès.\n";
                break;

            case '2':
                $members = $memberRepo->findAll();
                if (empty($members)) {
                    echo "Aucun membre trouvé.\n";
                } else {
                    echo "\n--- Liste des membres ---\n";
                    foreach ($members as $m) {
                        echo "ID: {$m['id']} | Nom: {$m['nom']} | Email: {$m['email']} | Créé: {$m['created_at']}\n";
                    }
                }
                break;

            case '3':
                echo "ID du membre: ";
                $id = (int)trim(fgets(STDIN));
                $member = $memberRepo->findById($id);
                if (!$member) {
                    echo "Membre non trouvé.\n";
                } else {
                    echo "\n--- Détails du membre ---\n";
                    echo "ID: {$member['id']}\n";
                    echo "Nom: {$member['nom']}\n";
                    echo "Email: {$member['email']}\n";
                    echo "Créé le: {$member['created_at']}\n";
                }
                break;

            case '4':
                echo "ID du membre à modifier: ";
                $id = (int)trim(fgets(STDIN));
                $member = $memberRepo->findById($id);
                if (!$member) {
                    echo "Membre non trouvé.\n";
                    break;
                }

                echo "Nouveau nom (actuel: {$member['nom']}): ";
                $nom = trim(fgets(STDIN));
                if (empty($nom)) $nom = $member['nom'];

                echo "Nouvel email (actuel: {$member['email']}): ";
                $email = trim(fgets(STDIN));
                if (empty($email)) $email = $member['email'];

                if ($email !== $member['email'] && $memberRepo->emailExists($email)) {
                    echo "Erreur: email déjà utilisé.\n";
                    break;
                }

                $memberRepo->update($id, $nom, $email);
                echo "Membre modifié avec succès.\n";
                break;

            case '5':
                echo "ID du membre à supprimer: ";
                $id = (int)trim(fgets(STDIN));
                $member = $memberRepo->findById($id);
                if (!$member) {
                    echo "Membre non trouvé.\n";
                    break;
                }

                if ($memberRepo->hasProjects($id)) {
                    echo "Erreur: impossible de supprimer un membre associé à des projets.\n";
                    break;
                }

                $memberRepo->delete($id);
                echo "Membre supprimé avec succès.\n";
                break;

            case '6':
                $members = $memberRepo->findAll();
                if (empty($members)) {
                    echo "Aucun membre disponible. Créez un membre d'abord.\n";
                    break;
                }

                echo "Titre du projet: ";
                $titre = trim(fgets(STDIN));

                echo "ID du membre: ";
                $membreId = (int)trim(fgets(STDIN));
                if (!$memberRepo->findById($membreId)) {
                    echo "Membre non trouvé.\n";
                    break;
                }

                echo "Type (court/long): ";
                $type = strtolower(trim(fgets(STDIN)));

                if ($type === 'court') {
                    echo "Durée (jours): ";
                    $duree = (int)trim(fgets(STDIN));
                    if ($duree <= 0) {
                        echo "Durée invalide.\n";
                        break;
                    }
                    $projet = new ProjetCourt($titre, $membreId, $duree);
                } elseif ($type === 'long') {
                    echo "Budget: ";
                    $budget = (float)trim(fgets(STDIN));
                    if ($budget <= 0) {
                        echo "Budget invalide.\n";
                        break;
                    }
                    $projet = new ProjetLong($titre, $membreId, $budget);
                } else {
                    echo "Type invalide. Utilisez 'court' ou 'long'.\n";
                    break;
                }

                $projectRepo->create($projet);
                echo "Projet créé avec succès.\n";
                break;

            case '7':
                $projets = $projectRepo->findAll();
                if (empty($projets)) {
                    echo "Aucun projet trouvé.\n";
                } else {
                    echo "\n--- Liste des projets ---\n";
                    foreach ($projets as $p) {
                        $details = "";
                        if ($p['type'] === 'court') {
                            $details = "Durée: {$p['duree']} jours";
                        } else {
                            $details = "Budget: {$p['budget']}";
                        }
                        echo "ID: {$p['id']} | Titre: {$p['titre']} | Type: {$p['type']} | {$details} | Membre ID: {$p['membre_id']} | Début: {$p['date_debut']}\n";
                    }
                }
                break;

            case '8':
                echo "ID du membre: ";
                $membreId = (int)trim(fgets(STDIN));
                if (!$memberRepo->findById($membreId)) {
                    echo "Membre non trouvé.\n";
                    break;
                }

                $projets = $projectRepo->findByMember($membreId);
                if (empty($projets)) {
                    echo "Aucun projet trouvé pour ce membre.\n";
                } else {
                    echo "\n--- Projets du membre ---\n";
                    foreach ($projets as $p) {
                        $details = "";
                        if ($p['type'] === 'court') {
                            $details = "Durée: {$p['duree']} jours";
                        } else {
                            $details = "Budget: {$p['budget']}";
                        }
                        echo "ID: {$p['id']} | Titre: {$p['titre']} | Type: {$p['type']} | {$details} | Début: {$p['date_debut']}\n";
                    }
                }
                break;

            case '9':
                echo "ID du projet à supprimer: ";
                $id = (int)trim(fgets(STDIN));
                $projet = $projectRepo->findById($id);
                if (!$projet) {
                    echo "Projet non trouvé.\n";
                    break;
                }

                if ($projectRepo->hasActiveActivities($id)) {
                    echo "Erreur: impossible de supprimer un projet avec des activités en cours.\n";
                    break;
                }

                $projectRepo->delete($id);
                echo "Projet supprimé avec succès.\n";
                break;

            case '10':
                $projets = $projectRepo->findAll();
                if (empty($projets)) {
                    echo "Aucun projet disponible. Créez un projet d'abord.\n";
                    break;
                }

                echo "Description de l'activité: ";
                $desc = trim(fgets(STDIN));
                echo "ID du projet: ";
                $projetId = (int)trim(fgets(STDIN));

                if (!$projectRepo->findById($projetId)) {
                    echo "Projet non trouvé.\n";
                    break;
                }

                $activite = new Activite($desc, $projetId);
                $activiteRepo->create($activite);
                echo "Activité ajoutée avec succès.\n";
                break;

            case '11':
                echo "ID de l'activité à modifier: ";
                $id = (int)trim(fgets(STDIN));
                $activite = $activiteRepo->findById($id);
                if (!$activite) {
                    echo "Activité non trouvée.\n";
                    break;
                }

                echo "Nouvelle description (actuelle: {$activite['description']}): ";
                $desc = trim(fgets(STDIN));
                if (empty($desc)) $desc = $activite['description'];

                echo "Nouveau statut (actuel: {$activite['statut']}) [en cours/terminée/annulée]: ";
                $statut = trim(fgets(STDIN));
                if (empty($statut)) $statut = $activite['statut'];

                $activiteRepo->update($id, $desc, $statut);
                echo "Activité modifiée avec succès.\n";
                break;

            case '12':
                echo "ID de l'activité à supprimer: ";
                $id = (int)trim(fgets(STDIN));
                $activite = $activiteRepo->findById($id);
                if (!$activite) {
                    echo "Activité non trouvée.\n";
                    break;
                }

                $activiteRepo->delete($id);
                echo "Activité supprimée avec succès.\n";
                break;

            case '13':
                echo "ID du projet: ";
                $projetId = (int)trim(fgets(STDIN));
                if (!$projectRepo->findById($projetId)) {
                    echo "Projet non trouvé.\n";
                    break;
                }

                $activites = $activiteRepo->findByProject($projetId);
                if (empty($activites)) {
                    echo "Aucune activité trouvée pour ce projet.\n";
                } else {
                    echo "\n--- Historique des activités ---\n";
                    foreach ($activites as $a) {
                        echo "ID: {$a['id']} | Description: {$a['description']} | Statut: {$a['statut']} | Date: {$a['date_activite']}\n";
                    }
                }
                break;

            case '0':
                echo "Au revoir !\n";
                exit;

            default:
                echo "Choix invalide. Veuillez entrer un numéro valide.\n";
                break;
        }
    } catch (Exception $e) {
        echo "Erreur: " . $e->getMessage() . "\n";
    }
}
?>
