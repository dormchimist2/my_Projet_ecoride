<?php

namespace App\Entity;

use App\Repository\PreferenceCdtRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreferenceCdtRepository::class)]
class PreferenceCdt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $accepteFumeurs = null;

    #[ORM\Column]
private ?bool $accepteAnimauxG = null;


    #[ORM\Column]
    private ?bool $accepteAnimauxP = null;

    #[ORM\Column]
    private ?bool $musiqueAutorisee = null;

    // Renommé 'discutionAutorisee' en 'discussionAutorisee'
    #[ORM\Column]
    private ?bool $discussionAutorisee = null;

    #[ORM\Column]
    private ?bool $siegeBebe = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $adPreference = null;

    #[ORM\OneToOne(inversedBy: 'preferenceCdt', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    /**
     * @var Collection<int, Covoiturage>
     */
    #[ORM\OneToMany(targetEntity: Covoiturage::class, mappedBy: 'preferenceCdt')]
    private Collection $covoiturages;

    public function __construct()
    {
        $this->covoiturages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAccepteFumeurs(): ?bool
    {
        return $this->accepteFumeurs;
    }

    public function setAccepteFumeurs(bool $accepteFumeurs): static
    {
        $this->accepteFumeurs = $accepteFumeurs;

        return $this;
    }

    public function getAccepteAnimauxG(): ?bool
    {
        return $this->accepteAnimauxG;
    }

    public function setAccepteAnimauxG(bool $accepteAnimauxG): static
    {
        $this->accepteAnimauxG = $accepteAnimauxG;

        return $this;
    }

    public function isAccepteAnimauxP(): ?bool
    {
        return $this->accepteAnimauxP;
    }

    public function setAccepteAnimauxP(bool $accepteAnimauxP): static
    {
        $this->accepteAnimauxP = $accepteAnimauxP;

        return $this;
    }

    public function isMusiqueAutorisee(): ?bool
    {
        return $this->musiqueAutorisee;
    }

    public function setMusiqueAutorisee(bool $musiqueAutorisee): static
    {
        $this->musiqueAutorisee = $musiqueAutorisee;

        return $this;
    }

    // Renommé 'getDiscutionAutorisee' en 'getDiscussionAutorisee'
    public function isDiscussionAutorisee(): ?bool
    {
        return $this->discussionAutorisee;
    }

    public function setDiscussionAutorisee(bool $discussionAutorisee): static
    {
        $this->discussionAutorisee = $discussionAutorisee;

        return $this;
    }

    public function isSiegeBebe(): ?bool
    {
        return $this->siegeBebe;
    }

    public function setSiegeBebe(bool $siegeBebe): static
    {
        $this->siegeBebe = $siegeBebe;

        return $this;
    }

    public function getAdPreference(): ?string
    {
        return $this->adPreference;
    }

    public function setAdPreference(?string $adPreference): static
    {
        $this->adPreference = $adPreference;

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

    /**
     * @return Collection<int, Covoiturage>
     */
    public function getCovoiturages(): Collection
    {
        return $this->covoiturages;
    }

    public function addCovoiturage(Covoiturage $covoiturage): static
    {
        if (!$this->covoiturages->contains($covoiturage)) {
            $this->covoiturages->add($covoiturage);
            $covoiturage->setPreferenceCdt($this);
        }

        return $this;
    }

    public function removeCovoiturage(Covoiturage $covoiturage): static
    {
        if ($this->covoiturages->removeElement($covoiturage)) {
            // set the owning side to null (unless already changed)
            if ($covoiturage->getPreferenceCdt() === $this) {
                $covoiturage->setPreferenceCdt(null);
            }
        }

        return $this;
    }
}
