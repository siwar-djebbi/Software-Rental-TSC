<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints\DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LocationRepository::class)]
class Location
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Key_token = null;

    #[ORM\Column]
    private ?bool $is_expired = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_exp = null;

    #[ORM\ManyToOne(inversedBy: 'location')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Logiciel $logiciel = null;

    #[ORM\ManyToOne(inversedBy: 'locations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyToken(): ?string
    {
        return $this->Key_token;
    }

    public function setKeyToken(string $Key_token): static
    {
        $this->Key_token = $Key_token;

        return $this;
    }

    public function isIsExpired(): ?bool
    {
        return $this->is_expired;
    }

    public function setIsExpired(bool $is_expired): static
    {
        $this->is_expired = $is_expired;

        return $this;
    }

    public function getDateExp(): ?\DateTimeInterface
    {
        return $this->date_exp;
    }

    public function setDateExp(\DateTimeInterface $date_exp): static
    {
        $this->date_exp = $date_exp;

        return $this;
    }

    public function getLogiciel(): ?Logiciel
    {
        return $this->logiciel;
    }

    public function setLogiciel(?Logiciel $logiciel): static
    {
        $this->logiciel = $logiciel;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }



}
