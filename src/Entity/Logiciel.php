<?php

namespace App\Entity;

use App\Repository\LogicielRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: LogicielRepository::class)]
class Logiciel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(length: 255)]
    private ?string $imagelogiciel = null;

    #[ORM\Column(length: 255)]
    private ?string $videologiciel = null;

    #[ORM\ManyToOne(inversedBy: 'logiciels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\OneToMany(mappedBy: 'logiciel', targetEntity: Location::class)]
    private ?Collection $location;

    public function __construct()
    {
        $this->location = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImagelogiciel(): ?string
    {
        return $this->imagelogiciel;
    }

    public function setImagelogiciel(string $imagelogiciel): static
    {
        $this->imagelogiciel = $imagelogiciel;

        return $this;
    }

    public function getVideologiciel(): ?string
    {
        return $this->videologiciel;
    }

    public function setVideologiciel(string $videologiciel): static
    {
        $this->videologiciel = $videologiciel;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocation(): Collection
    {
        return $this->location;
    }

    public function addLocation(Location $location): static
    {
        if (!$this->location->contains($location)) {
            $this->location->add($location);
            $location->setLogiciel($this);
        }

        return $this;
    }

    public function removeLocation(Location $location): static
    {
        if ($this->location->removeElement($location)) {
            // set the owning side to null (unless already changed)
            if ($location->getLogiciel() === $this) {
                $location->setLogiciel(null);
            }
        }

        return $this;
    }


}
