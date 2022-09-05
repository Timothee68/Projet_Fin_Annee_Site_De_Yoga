<?php

namespace App\Entity;

use App\Repository\BenefitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BenefitRepository::class)
 */
class Benefit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity=Session::class, mappedBy="benefit")
     */
    private $sessions;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="benefits")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
     * @ORM\ManyToMany(targetEntity=Input::class, mappedBy="benefits")
     */
    private $inputs;

    /**
     * @ORM\OneToMany(targetEntity=ImgCollectionBenefit::class, mappedBy="benfefit" , cascade={"persist"} , orphanRemoval=true)
     * @ORM\OrderBy({"img" = "ASC"})
     */
    private $imgCollectionBenefits;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->inputs = new ArrayCollection();
        $this->imgCollectionBenefits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setBenefit($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session)) {
            // set the owning side to null (unless already changed)
            if ($session->getBenefit() === $this) {
                $session->setBenefit(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
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

    /**
     * @return Collection<int, Input>
     */
    public function getInputs(): Collection
    {
        return $this->inputs;
    }

    public function addInput(Input $input): self
    {
        if (!$this->inputs->contains($input)) {
            $this->inputs[] = $input;
            $input->addBenefit($this);
        }

        return $this;
    }

    public function removeInput(Input $input): self
    {
        if ($this->inputs->removeElement($input)) {
            $input->removeBenefit($this);
        }

        return $this;
    }

    
    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return Collection<int, ImgCollectionBenefit>
     */
    public function getImgCollectionBenefits(): Collection
    {
        return $this->imgCollectionBenefits;
    }

    public function addImgCollectionBenefit(ImgCollectionBenefit $imgCollectionBenefit): self
    {
        if (!$this->imgCollectionBenefits->contains($imgCollectionBenefit)) {
            $this->imgCollectionBenefits[] = $imgCollectionBenefit;
            $imgCollectionBenefit->setBenefit($this);
        }

        return $this;
    }

    public function removeImgCollectionBenefit(ImgCollectionBenefit $imgCollectionBenefit): self
    {
        if ($this->imgCollectionBenefits->removeElement($imgCollectionBenefit)) {
            // set the owning side to null (unless already changed)
            if ($imgCollectionBenefit->getBenefit() === $this) {
                $imgCollectionBenefit->setBenefit(null);
            }
        }

        return $this;
    }
}
