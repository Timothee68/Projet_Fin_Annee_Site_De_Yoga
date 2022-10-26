<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SessionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=SessionRepository::class)
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endTime;

    /**
     * @ORM\ManyToOne(targetEntity=Benefit::class, inversedBy="sessions" , fetch="EAGER")
     */
    private $benefit;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="session" , fetch="EAGER")
     */
    private $reservations;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *  min = 0,
     *  max = 50,
     *  notInRangeMessage = "Arrondis interface : Veuillez entrer une valeur comprise entre {{ min }} et {{ max }}."
     * )
     */
    private $nbPlaceMax;

    /**
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $backgroundColor;


    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }


    public function getBenefit(): ?Benefit
    {
        return $this->benefit;
    }

    public function setBenefit(?Benefit $benefit): self
    {
        $this->benefit = $benefit;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setSession($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getSession() === $this) {
                $reservation->setSession(null);
            }
        }

        return $this;
    }

    public function getNbPlaceMax(): ?int
    {
        return $this->nbPlaceMax;
    }

    public function setNbPlaceMax(int $nbPlaceMax): self
    {
        $this->nbPlaceMax = $nbPlaceMax;

        return $this;
    }


    // fonction pour afficher le nombre de place restante dans la session 
    public function getRemainingPlaces(){
        $somme = 0;
        foreach($this->reservations as $reservation)
        {
            $res = $reservation->getNbPlaces();
            $somme += $res;
        }    
        $reserved =  $this->nbPlaceMax - $somme;
            return $reserved;
     }


     // fonction pour afficher le nombre de place réserver pour la session 
     public function getNbPlaceReserved(){
        $somme = 0;
        foreach($this->reservations as $reservation)
        {
            $res= $reservation->getNbPlaces();
            $somme += $res;          
        }
        return $somme;
    }

   public function diffDate()
   {
        $date = new DateTime();
        $origin =  $this->startTime->getTimestamp();
        $target = $date->getTimestamp();
        $timeDiff = $origin - $target;
        
        echo $timeDiff ;
        
   }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

}
