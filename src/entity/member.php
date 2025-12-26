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

    public function getId(): int
    {
        return $this->id;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Email invalide");
        }
        $this->email = $email;
    }

    public function getCreatedAt(): DateTime
    {
    	return $this->createdAt;
    } 
}

/*$showCase = new Membre('aymane', 'dominantvortex@proton.me');
$b = $showCase->getNom();
echo ($b);

echo ("\n");
$showCase->setNom('noufal');
$c = $showCase->getNom();

echo ($c);


//echo $showCase->getNom();

//$showCase->setEmail("LaksimiAyman@proton.me");
//$showCase->getEmail();
//echo $showCase->getEmail();
//$showCase->setEmail("oldman@proton.me"); // chnage 
//echo $showCase->getEmail();

echo ("\n");
 */
