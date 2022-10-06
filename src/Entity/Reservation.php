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
}
