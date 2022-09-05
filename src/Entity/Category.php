<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categoryName;

    /**
     * @ORM\OneToMany(targetEntity=Benefit::class, mappedBy="category")
     */
    private $benefits;

    public function __construct()
    {
        $this->benefits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    /**
     * @return Collection<int, Benefit>
     */
    public function getBenefits(): Collection
    {
        return $this->benefits;
    }

    // public function showBenefitByCategory()
    // {
    //     foreach($this->benefits as $benefit)
    //     {
    //       echo   '<div class="card" style="width: 18rem;">',
    //                 '<div class="card-body">',
    //                     '<h5 class="card-title">'.$benefit->getTitle().'</h5>',
    //                         '<img src="..." class="card-img-top" alt="Photo représentant le service proposé ">',
    //                     '<p class="card-text">With supporting text below as a natural lead-in to additional content.</p>',
    //                     '<a href=" {{ path("detail_benefit, {"id: $benefit->getId() }) "}} " class="btn btn-primary">Go somewhere</a>',
    //                 '</div>',
    //             '</div>';

    //     }
    // }

    public function addBenefit(Benefit $benefit): self
    {
        if (!$this->benefits->contains($benefit)) {
            $this->benefits[] = $benefit;
            $benefit->setCategory($this);
        }

        return $this;
    }

    public function removeBenefit(Benefit $benefit): self
    {
        if ($this->benefits->removeElement($benefit)) {
            // set the owning side to null (unless already changed)
            if ($benefit->getCategory() === $this) {
                $benefit->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
    
           return $this->categoryName;   
    }
}
