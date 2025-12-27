# Guide d'Apprentissage : Construire une Application PHP Orientée Objet

## Table des Matières
1. [Introduction](#introduction)
2. [Architecture du Projet](#architecture-du-projet)
3. [Étape par Étape : De Zéro à Héro](#étape-par-étape)
4. [Concepts Clés](#concepts-clés)
5. [Bonnes Pratiques](#bonnes-pratiques)
6. [Dépannage](#dépannage)

---

## Introduction

Ce guide vous apprendra à construire une application PHP orientée objet (OOP) complète avec CRUD, base de données, et architecture propre. Nous utiliserons le projet Metis comme exemple.

---

## Architecture du Projet

### Structure des Dossiers

```
Metis/
├── database/
│   ├── database.php          # Connexion PDO
│   └── schema.sql            # Structure de la base de données
├── src/
│   ├── entity/               # Classes métier (modèles)
│   │   ├── member.php
│   │   ├── projet.php
│   │   ├── projetCourt.php
│   │   ├── projetLong.php
│   │   └── activite.php
│   ├── repository/           # Accès aux données (DAO)
│   │   ├── memberRepository.php
│   │   ├── projectRepository.php
│   │   └── activiteRepository.php
│   └── app/
│       └── console.php       # Point d'entrée de l'application
└── .gitignore
```

### Séparation des Responsabilités

- **Entity** : Représente les données métier (Member, Projet, Activite)
- **Repository** : Gère l'accès à la base de données (CRUD)
- **App** : Interface utilisateur (console dans notre cas)

---

## Étape par Étape : De Zéro à Héro

### ÉTAPE 1 : Analyser les Besoins

**Avant de coder, répondez à ces questions :**
- Quelles sont les entités principales ? (Membre, Projet, Activité)
- Quelles sont les relations entre elles ? (Un membre a plusieurs projets, un projet a plusieurs activités)
- Quelles opérations sont nécessaires ? (Créer, Lire, Modifier, Supprimer)

**Action :** Créez un diagramme UML de classes pour visualiser.

---

### ÉTAPE 2 : Concevoir la Base de Données

**Règles importantes :**
1. Chaque entité = une table
2. Relations = clés étrangères (FOREIGN KEY)
3. Contraintes d'intégrité (UNIQUE, NOT NULL)

**Exemple pour Metis :**

```sql
CREATE TABLE membres (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL
);
```

**Points clés :**
- `SERIAL` = auto-increment (PostgreSQL)
- `PRIMARY KEY` = identifiant unique
- `UNIQUE` = pas de doublons (pour email)
- `NOT NULL` = champ obligatoire

**Action :** Écrivez votre `schema.sql` avec toutes les tables et relations.

---

### ÉTAPE 3 : Créer la Classe de Connexion Database

**Pourquoi une classe Database ?**
- Réutilisable partout
- Singleton : une seule connexion
- Configuration centralisée

**Code de base :**

```php
<?php
class Database 
{
    private static $host = 'localhost';
    private static $dbname = 'metis';
    private static $user = 'metis_user';
    private static $password = 'metis123';
    private static ?PDO $connection = null;

    private function __construct() {}

    public static function connect() {
        if (self::$connection === null) {
            self::$connection = new PDO(
                "pgsql:host=" . self::$host . ";dbname=" . self::$dbname,
                self::$user,
                self::$password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$connection;
    }
}
```

**Concepts :**
- `static` = méthode/classe accessible sans instancier
- `private __construct()` = empêche `new Database()`
- `PDO::ERRMODE_EXCEPTION` = erreurs en exceptions

**Action :** Créez `database/database.php`.

---

### ÉTAPE 4 : Créer les Entités (Entity Classes)

**Qu'est-ce qu'une entité ?**
Une classe qui représente une donnée métier avec ses propriétés et règles de validation.

**Structure d'une entité :**

```php
<?php
class Member
{
    private int $id;
    private string $nom;
    private string $email;
    private DateTime $createdAt;

    public function __construct(string $nom, string $email)
    {
        $this->setNom($nom);
        $this->setEmail($email);
        $this->createdAt = new DateTime();
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        if (empty($nom)) {
            throw new InvalidArgumentException("Le nom ne peut pas être vide");
        }
        $this->nom = $nom;
    }
}
```

**Règles importantes :**
1. Propriétés en `private` (encapsulation)
2. Constructeur pour initialiser
3. Getters pour lire
4. Setters pour écrire + validation
5. Validation dans les setters

**Action :** Créez toutes vos entités avec getters/setters.

---

### ÉTAPE 5 : Héritage et Classes Abstraites

**Quand utiliser l'héritage ?**
Quand plusieurs classes partagent des propriétés communes.

**Exemple : ProjetCourt et ProjetLong héritent de Projet**

```php
abstract class Projet
{
    protected int $id;
    protected string $titre;
    protected DateTime $dateDebut;
    protected int $membreId;

    public function __construct(string $titre, int $membreId)
    {
        $this->setTitre($titre);
        $this->membreId = $membreId;
        $this->dateDebut = new DateTime();
    }

    abstract public function getType(): string;
}

class ProjetCourt extends Projet
{
    private int $duree;

    public function __construct(string $titre, int $membreId, int $duree)
    {
        parent::__construct($titre, $membreId);
        $this->setDuree($duree);
    }

    public function getType(): string
    {
        return 'court';
    }
}
```

**Concepts :**
- `abstract class` = ne peut pas être instanciée directement
- `extends` = héritage
- `protected` = accessible aux classes enfants
- `parent::__construct()` = appelle le constructeur parent
- `abstract method` = doit être implémentée dans les enfants

**Action :** Créez vos classes avec héritage si nécessaire.

---

### ÉTAPE 6 : Créer les Repositories (Accès aux Données)

**Qu'est-ce qu'un Repository ?**
Une classe qui gère toutes les opérations CRUD sur une entité.

**Structure d'un Repository :**

```php
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

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM membres WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
```

**Règles de sécurité CRITIQUES :**
1. **TOUJOURS utiliser `prepare()` et `execute()`** (pas de concaténation SQL)
2. **Utiliser des paramètres nommés** (`:nom`, `:email`)
3. **Jamais de variables directement dans la requête**

**Mauvais (DANGEREUX) :**
```php
$stmt = $pdo->query("SELECT * FROM membres WHERE id = " . $id); // ❌ INJECTION SQL
```

**Bon (SÉCURISÉ) :**
```php
$stmt = $pdo->prepare("SELECT * FROM membres WHERE id = :id");
$stmt->execute(['id' => $id]); // ✅ SÉCURISÉ
```

**Action :** Créez tous vos repositories avec CRUD complet.

---

### ÉTAPE 7 : Gérer les Relations et Contraintes

**Exemple : Supprimer un membre seulement s'il n'a pas de projets**

```php
public function hasProjects(int $id): bool
{
    $stmt = $this->pdo->prepare(
        "SELECT COUNT(*) FROM projets WHERE membre_id = :id"
    );
    $stmt->execute(['id' => $id]);
    return $stmt->fetchColumn() > 0;
}

public function delete(int $id): void
{
    if ($this->hasProjects($id)) {
        throw new Exception("Impossible de supprimer un membre avec des projets");
    }
    $stmt = $this->pdo->prepare("DELETE FROM membres WHERE id = :id");
    $stmt->execute(['id' => $id]);
}
```

**Action :** Ajoutez des vérifications avant chaque suppression.

---

### ÉTAPE 8 : Créer l'Interface Utilisateur (Console)

**Structure d'un menu console :**

```php
<?php
require_once __DIR__ . '/../../database/database.php';
require_once __DIR__ . '/../repository/memberRepository.php';
require_once __DIR__ . '/../entity/member.php';

$memberRepo = new MemberRepository();

function printMenu() {
    echo "\n=== METIS ===\n";
    echo "1. Créer un membre\n";
    echo "2. Lister les membres\n";
    echo "0. Quitter\n";
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
            echo "Membre créé avec succès.\n";
            break;

        case '2':
            $members = $memberRepo->findAll();
            foreach ($members as $m) {
                echo "ID: {$m['id']} | Nom: {$m['nom']} | Email: {$m['email']}\n";
            }
            break;

        case '0':
            exit;
    }
}
```

**Concepts :**
- `fgets(STDIN)` = lire l'entrée utilisateur
- `trim()` = enlever espaces
- `switch/case` = menu
- `while(true)` = boucle infinie

**Action :** Créez votre console avec tous les menus.

---

## Concepts Clés

### 1. Programmation Orientée Objet (OOP)

**4 Piliers :**
- **Encapsulation** : Propriétés privées, accès via getters/setters
- **Héritage** : Classes enfants héritent du parent
- **Polymorphisme** : Même méthode, comportements différents
- **Abstraction** : Classes abstraites, méthodes abstraites

### 2. PDO (PHP Data Objects)

**Pourquoi PDO ?**
- Sécurisé (requêtes préparées)
- Portable (plusieurs bases de données)
- Moderne (remplace mysql_*)

**Méthodes essentielles :**
- `prepare()` : Prépare une requête
- `execute()` : Exécute avec paramètres
- `fetch()` : Récupère une ligne
- `fetchAll()` : Récupère toutes les lignes
- `fetchColumn()` : Récupère une colonne

### 3. CRUD

**Create (Créer) :**
```php
INSERT INTO table (col1, col2) VALUES (:val1, :val2)
```

**Read (Lire) :**
```php
SELECT * FROM table WHERE id = :id
```

**Update (Modifier) :**
```php
UPDATE table SET col1 = :val1 WHERE id = :id
```

**Delete (Supprimer) :**
```php
DELETE FROM table WHERE id = :id
```

---

## Bonnes Pratiques

### 1. Validation des Données

**Toujours valider dans les setters :**
```php
public function setEmail(string $email): void
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException("Email invalide");
    }
    $this->email = $email;
}
```

### 2. Gestion des Erreurs

**Utiliser try/catch :**
```php
try {
    $member = new Member($nom, $email);
    $memberRepo->create($member);
    echo "Succès.\n";
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
```

### 3. Noms de Variables Clairs

**Mauvais :**
```php
$m = new Member($n, $e);
```

**Bon :**
```php
$member = new Member($nom, $email);
```

### 4. Commentaires Utiles

**Ne pas commenter l'évident :**
```php
// Créer un membre ❌
$member = new Member($nom, $email);
```

**Commenter la logique complexe :**
```php
// Vérifier qu'aucun projet n'est associé avant suppression
if ($memberRepo->hasProjects($id)) {
    throw new Exception("Impossible de supprimer");
}
```

---

## Dépannage

### Erreur : "Class not found"
**Solution :** Vérifiez les `require_once` et les chemins.

### Erreur : "SQLSTATE[HY000]"
**Solution :** Vérifiez la connexion à la base de données (host, user, password).

### Erreur : "Call to undefined method"
**Solution :** Vérifiez que la méthode existe dans la classe.

### Erreur : "Cannot access private property"
**Solution :** Utilisez les getters/setters au lieu d'accéder directement.

### Erreur : "Fatal error: Uncaught Error"
**Solution :** Vérifiez les types de paramètres et les valeurs null.

---

## Checklist de Développement

Avant de considérer votre projet terminé :

- [ ] Toutes les entités créées avec getters/setters
- [ ] Tous les repositories avec CRUD complet
- [ ] Validation des données dans les setters
- [ ] Vérifications avant suppressions
- [ ] Requêtes préparées (sécurité)
- [ ] Gestion des erreurs (try/catch)
- [ ] Console fonctionnelle avec tous les menus
- [ ] Base de données créée et testée
- [ ] Code sans commentaires de debug
- [ ] .gitignore configuré

---

## Ressources pour Aller Plus Loin

1. **PHP The Right Way** : https://phptherightway.com/
2. **PDO Tutorial** : Documentation officielle PHP
3. **UML** : Apprenez à créer des diagrammes de classes
4. **Design Patterns** : Repository, Singleton, Factory

---

## Conclusion

Vous avez maintenant les bases pour construire une application PHP OOP complète. La clé est de :
1. Planifier avant de coder
2. Séparer les responsabilités
3. Valider les données
4. Sécuriser les requêtes SQL
5. Tester chaque fonctionnalité

**Bon courage ! 🚀**

