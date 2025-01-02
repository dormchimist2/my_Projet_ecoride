<?php

namespace App\Entity;

use App\Repository\CovoiturageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: CovoiturageRepository::class)]
#[Broadcast]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDeDepart = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureDeDepart = null;

    #[ORM\Column(length: 50)]
    private ?string $lieuDeDepart = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateArrivee = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureArrivee = null;

    #[ORM\Column(length: 50)]
    private ?string $lieuArrivee = null;

    #[ORM\Column]
    private ?int $nombreDePlace = null;

    #[ORM\Column]
    private ?float $prixPersonne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDeDepart(): ?\DateTimeInterface
    {
        return $this->dateDeDepart;
    }

    public function setDateDeDepart(\DateTimeInterface $dateDeDepart): static
    {
        $this->dateDeDepart = $dateDeDepart;

        return $this;
    }

    public function getHeureDeDepart(): ?\DateTimeInterface
    {
        return $this->heureDeDepart;
    }

    public function setHeureDeDepart(\DateTimeInterface $heureDeDepart): static
    {
        $this->heureDeDepart = $heureDeDepart;

        return $this;
    }

    public function getLieuDeDepart(): ?string
    {
        return $this->lieuDeDepart;
    }

    public function setLieuDeDepart(string $lieuDeDepart): static
    {
        $this->lieuDeDepart = $lieuDeDepart;

        return $this;
    }

    public function getDateArrivee(): ?\DateTimeInterface
    {
        return $this->dateArrivee;
    }

    public function setDateArrivee(\DateTimeInterface $dateArrivee): static
    {
        $this->dateArrivee = $dateArrivee;

        return $this;
    }

    public function getHeureArrivee(): ?\DateTimeInterface
    {
        return $this->heureArrivee;
    }

    public function setHeureArrivee(\DateTimeInterface $heureArrivee): static
    {
        $this->heureArrivee = $heureArrivee;

        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieuArrivee;
    }

    public function setLieuArrivee(string $lieuArrivee): static
    {
        $this->lieuArrivee = $lieuArrivee;

        return $this;
    }

    public function getNombreDePlace(): ?int
    {
        return $this->nombreDePlace;
    }

    public function setNombreDePlace(int $nombreDePlace): static
    {
        $this->nombreDePlace = $nombreDePlace;

        return $this;
    }

    public function getPrixPersonne(): ?float
    {
        return $this->prixPersonne;
    }

    public function setPrixPersonne(float $prixPersonne): static
    {
        $this->prixPersonne = $prixPersonne;

        return $this;
    }
}
