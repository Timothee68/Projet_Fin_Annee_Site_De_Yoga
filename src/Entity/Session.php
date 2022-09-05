<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="datetime")
     */
    private $sessionDate;

    /**
     * @ORM\ManyToOne(targetEntity=Benefit::class, inversedBy="sessions")
     */
    private $benefit;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="session")
     */
    private $reservations;

    /**
     * @ORM\Column(type="integer")
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

    public function getSessionDate(): ?\DateTimeInterface
    {
        return $this->sessionDate;
    }

    public function setSessionDate(\DateTimeInterface $sessionDate): self
    {
        $this->sessionDate = $sessionDate;

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
