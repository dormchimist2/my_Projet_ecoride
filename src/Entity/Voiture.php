<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $marque = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $modele = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $couleur = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateMatriculation = null;

    #[ORM\Column(length: 50)]
    private ?string $nImmatriculation = null;

    #[ORM\Column(length: 50)]
    private ?string $moteur = null;

    #[ORM\ManyToOne(inversedBy: 'voitures')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getDateMatriculation(): ?\DateTimeInterface
    {
        return $this->dateMatriculation;
    }

    public function setDateMatriculation(\DateTimeInterface $dateMatriculation): static
    {
        $this->dateMatriculation = $dateMatriculation;

        return $this;
    }

    public function getNImmatriculation(): ?string
    {
        return $this->nImmatriculation;
    }

    public function setNImmatriculation(string $nImmatriculation): static
    {
        $this->nImmatriculation = $nImmatriculation;

        return $this;
    }

    public function getMoteur(): ?string
    {
        return $this->moteur;
    }

    public function setMoteur(string $moteur): static
    {
        $this->moteur = $moteur;

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
}
