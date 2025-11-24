<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé.')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $copyright = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $altImage = null;

    #[ORM\Column(length: 255)]
    private ?string $altMotif = null;

    #[ORM\Column(length: 255)]
    private ?string $metaDescription = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Recipe::class)]
    private Collection $recipes;

    #[Vich\UploadableField(mapping: 'categories_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $imageUpdatedAt = null;

    #[Vich\UploadableField(mapping: 'categories_motifs', fileNameProperty: 'motifName')]
    private ?File $motifFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $motifName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $motifUpdatedAt = null;
    
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    public function setCopyright(string $copyright): self
    {
        $this->copyright = $copyright;

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

    public function getAltImage(): ?string
    {
        return $this->altImage;
    }

    public function setAltImage(string $altImage): self
    {
        $this->altImage = $altImage;

        return $this;
    }

    public function getAltMotif(): ?string
    {
        return $this->altMotif;
    }

    public function setAltMotif(string $altMotif): self
    {
        $this->altMotif = $altMotif;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

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
    public function getMotifUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->motifUpdatedAt;
    }

    public function setMotifFile(?File $motifFile = null): void
    {
        $this->motifFile = $motifFile;

        if (null !== $motifFile) {
            $this->motifUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getMotifFile(): ?File
    {
        return $this->motifFile;
    }

    public function setMotifName(?string $motifName): void
    {
        $this->motifName = $motifName;
    }

    public function getMotifName(): ?string
    {
        return $this->motifName;
    }
    public function getImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->imageUpdatedAt;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
            $recipe->setCategory($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getCategory() === $this) {
                $recipe->setCategory(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
