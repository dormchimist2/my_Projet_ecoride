<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $note = null;

    #[ORM\Column(length: 250, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    private ?Userx $auteur = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    private ?Userx $conducteur = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    private ?Covoiturage $trajet = null;

    
public const STATUS_PENDING = 'pending';
public const STATUS_ACCEPTED = 'accepted';
public const STATUS_REJECTED = 'rejected';

    #[ORM\Column(length: 20)]
private string $status = 'pending'; // valeurs possibles : 'pending', 'accepted', 'rejected'


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }


    public function setNote(?int $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getAuteur(): ?Userx
    {
        return $this->auteur;
    }

    public function setAuteur(?Userx $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getConducteur(): ?Userx
    {
        return $this->conducteur;
    }

    public function setConducteur(?Userx $conducteur): static
    {
        $this->conducteur = $conducteur;

        return $this;
    }

    public function getTrajet(): ?Covoiturage
    {
        return $this->trajet;
    }

    public function setTrajet(?Covoiturage $trajet): static
    {
        $this->trajet = $trajet;

        return $this;
    }

   public function getStatus(): string
{
    return $this->status;
}

public function setStatus(string $status): self
{
    $this->status = $status;
    return $this;
}


}
