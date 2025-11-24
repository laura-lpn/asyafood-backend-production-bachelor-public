<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[Vich\Uploadable]
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'ingredient', targetEntity: IngredientRecipe::class, orphanRemoval: true)]
    private Collection $recipes;

    #[ORM\Column]
    private ?bool $isLiquid = null;

    #[ORM\Column(length: 255)]
    private ?string $namePlurial = null;

    #[Vich\UploadableField(mapping: 'ingredients_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $imageUpdatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $altImage = null;

    #[ORM\Column]
    private ?bool $isIndexed = null;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, IngredientRecipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(IngredientRecipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
            $recipe->setIngredient($this);
        }

        return $this;
    }

    public function removeRecipe(IngredientRecipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getIngredient() === $this) {
                $recipe->setIngredient(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->name;
    }

    public function isLiquid(): ?bool
    {
        return $this->isLiquid;
    }

    public function setIsLiquid(bool $isLiquid): static
    {
        $this->isLiquid = $isLiquid;

        return $this;
    }

    public function getNamePlurial(): ?string
    {
        return $this->namePlurial;
    }

    public function setNamePlurial(?string $namePlurial): static
    {
        $this->namePlurial = $namePlurial;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->imageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    
    public function getImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->imageUpdatedAt;
    }

    public function getAltImage(): ?string
    {
        return $this->altImage;
    }

    public function setAltImage(?string $altImage): static
    {
        $this->altImage = $altImage;

        return $this;
    }

    public function isIndexed(): ?bool
    {
        return $this->isIndexed;
    }

    public function setIsIndexed(bool $isIndexed): static
    {
        $this->isIndexed = $isIndexed;

        return $this;
    }
}
