<?php

require_once __DIR__ . '/../entity/member.php';
require_once __DIR__ . '/../repository/memberRepository.php';



echo "=== METIS - Gestion des Membres ===\n";

$validChoices = ['1', '2'];
$choice = null;


while (!in_array($choice, $validChoices, true)) {
    echo "1. Ajouter un membre\n";
    echo "2. Lister les membres\n";
    echo "Choix: ";
    
    $choice = trim(fgets(STDIN));

    if (!in_array($choice, $validChoices, true)) {
        echo "le Choix invalide. SVP entrer 1 ou 2.\n\n";
    }
}

$repo = new MemberRepository();

if ($choice === '1') {
    echo "Nom: ";
    $nom = trim(fgets(STDIN));

    echo "Email: ";
    $email = trim(fgets(STDIN));

    $member = new Member($nom, $email);
    $repo->create($member);

    echo "membre ajoute avec succes.\n";
}

if ($choice === '2') {
    $members = $repo->findAll();
    print_r($members);
}

