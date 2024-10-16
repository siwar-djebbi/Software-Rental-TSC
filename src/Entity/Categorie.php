<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Logiciel::class)]
    private ?Collection $logiciels;

    public function __construct()
    {
        $this->logiciels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->label ?: ''; // Adjust this according to your needs
    }
    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, Logiciel>
     */
    public function getLogiciels(): Collection
    {
        return $this->logiciels;
    }

    public function addLogiciel(Logiciel $logiciel): static
    {
        if (!$this->logiciels->contains($logiciel)) {
            $this->logiciels->add($logiciel);
            $logiciel->setCategorie($this);
        }

        return $this;
    }

    public function removeLogiciel(Logiciel $logiciel): static
    {
        if ($this->logiciels->removeElement($logiciel)) {
            // set the owning side to null (unless already changed)
            if ($logiciel->getCategorie() === $this) {
                $logiciel->setCategorie(null);
            }
        }

        return $this;
    }

}