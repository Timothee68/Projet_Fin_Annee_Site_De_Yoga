<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\NewsletterRepository;

/**
 * @ORM\Entity(repositoryClass=NewsletterRepository::class)
 */
class Newsletter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRegister;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSigneUp ;

    public function __construct()
    {
        $this->dateSigneUp = new DateTime();
        $this->isRegister= false; 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsRegister(): ?bool
    {
        return $this->isRegister;
    }

    public function setIsRegister(bool $isRegister): self
    {
        $this->isRegister = $isRegister;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDateSigneUp(): ?\DateTimeInterface
    {
        return $this->dateSigneUp;
    }

    public function setDateSigneUp(\DateTimeInterface $dateSigneUp): self
    {
        $this->dateSigneUp = $dateSigneUp;

        return $this;
    }
}
