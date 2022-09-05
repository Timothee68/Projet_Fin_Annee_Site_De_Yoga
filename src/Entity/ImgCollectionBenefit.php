<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\ImgCollectionBenefitRepository;

/**
 * @ORM\Entity(repositoryClass=ImgCollectionBenefitRepository::class)
 */
class ImgCollectionBenefit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $alt = 'Image représentant Om Nada Bramha pendant la pratique de :';

    /**
     * @ORM\ManyToOne(targetEntity=Benefit::class, inversedBy="imgCollectionBenefits")
     */
    private $benefit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

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
}
