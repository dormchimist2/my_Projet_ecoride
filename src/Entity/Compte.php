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
    private string $solde = "50.00"; // 50 crédits offerts par défaut

    #[ORM\OneToOne(inversedBy: 'compte', cascade: ['persist'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolde(): float
    {
        return (float) $this->solde; // Assure la conversion 
    }

    public function setSolde(float $solde): static
    {
        $this->solde = number_format($solde, 2, '.', ''); // Assure la bonne précision
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function credit(float $montant): self
    {
        $this->solde = number_format((float) $this->solde + $montant, 2, '.', '');
        return $this;
    }

    public function debit(float $montant): self
    {
        if ((float) $this->solde >= $montant) {
            $this->solde = number_format((float) $this->solde - $montant, 2, '.', '');
        } else {
            throw new \Exception("Solde insuffisant !");
        }
        return $this;
    }
}
