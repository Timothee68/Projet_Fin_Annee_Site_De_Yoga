<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

   /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *  min = 0,
     *  max = 50,
     *  notInRangeMessage = "Arrondis interface : Veuillez entrer une valeur comprise entre {{ min }} et {{ max }}."
     * )
     */
    private $nbPlace;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservations" )
     */
    private $user;
        
    /**
     * @ORM\ManyToOne(targetEntity=Session::class, inversedBy="reservations" , fetch="EAGER")
     */
    private $session;

    /**
     * @ORM\ManyToOne(targetEntity=Stage::class, inversedBy="reservations" , fetch="EAGER")
     */
    private $stage;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $telephone;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateOfBirth;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function nbPlace(){
        return $this->nbPlace;
    }
    public function getNbPlaces(): ?int
    {
        return $this->nbPlace;
    }

    public function setNbPlaces(int $nbPlace): self
    {
        $this->nbPlace = $nbPlace;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function __toString()
    {
        return $this->nbPlace." place(s)";
    }

    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    public function setStage(?Stage $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }
}
