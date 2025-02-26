<?php
namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $solde = 50.00; // 50 crédits par défaut

    #[ORM\OneToOne(inversedBy: 'compte', cascade: ['persist'])]
    private ?Userx $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolde(): float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): static
    {
        $this->solde = round($solde, 2); 
        return $this;
    }

    public function getUser(): ?Userx
    {
        return $this->user;
    }

    public function setUser(?Userx $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function credit(float $montant): self
    {
        $this->solde = round($this->solde + $montant, 2);
        return $this;
    }

    public function debit(float $montant): self
    {
        if ($this->solde >= $montant) {
            $this->solde = round($this->solde - $montant, 2);
        } else {
            throw new \Exception("Solde insuffisant !");
        }
        return $this;
    }
}
