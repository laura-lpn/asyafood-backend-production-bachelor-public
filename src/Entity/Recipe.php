<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity(fields: ['slug'], message: 'Ce slug est déjà utilisé.')]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $genre = null;

    #[ORM\Column(length: 255)]
    private ?string $metaDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $altImage = null;

    #[ORM\Column]
    private ?int $modulo = null;

    #[ORM\Column(length: 255)]
    private ?string $unitModulo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $info = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: IngredientRecipe::class, orphanRemoval: true)]
    private Collection $ingredients;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: TimeRecipe::class, orphanRemoval: true)]
    private Collection $times;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Step::class, orphanRemoval: true)]
    private Collection $steps;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'recipes')]
    private Collection $users;

    #[Vich\UploadableField(mapping: 'recipes_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $imageUpdatedAt = null;

    #[Vich\UploadableField(mapping: 'recipes_prints', fileNameProperty: 'printName')]
    private ?File $printFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $printName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $printUpdatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->times = new ArrayCollection();
        $this->steps = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

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

    public function getAltImage(): ?string
    {
        return $this->altImage;
    }

    public function setAltImage(string $altImage): self
    {
        $this->altImage = $altImage;

        return $this;
    }

    public function getModulo(): ?int
    {
        return $this->modulo;
    }

    public function setModulo(int $modulo): self
    {
        $this->modulo = $modulo;

        return $this;
    }

    public function getUnitModulo(): ?string
    {
        return $this->unitModulo;
    }

    public function setUnitModulo(string $unitModulo): self
    {
        $this->unitModulo = $unitModulo;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(string $info): self
    {
        $this->info = $info;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
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

    public function setPrintFile(?File $printFile = null): void
    {
        $this->printFile = $printFile;

        if (null !== $printFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->printUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getPrintFile(): ?File
    {
        return $this->printFile;
    }

    public function setPrintName(?string $printName): void
    {
        $this->printName = $printName;
    }

    public function getPrintName(): ?string
    {
        return $this->printName;
    }
    
    public function getPrintUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->printUpdatedAt;
    }

    /**
     * @return Collection<int, IngredientRecipe>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(IngredientRecipe $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(IngredientRecipe $ingredient): self
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipe() === $this) {
                $ingredient->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TimeRecipe>
     */
    public function getTimes(): Collection
    {
        return $this->times;
    }

    public function addTime(TimeRecipe $time): self
    {
        if (!$this->times->contains($time)) {
            $this->times->add($time);
            $time->setRecipe($this);
        }

        return $this;
    }

    public function removeTime(TimeRecipe $time): self
    {
        if ($this->times->removeElement($time)) {
            // set the owning side to null (unless already changed)
            if ($time->getRecipe() === $this) {
                $time->setRecipe(null);
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

    /**
     * @return Collection<int, Step>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
            $step->setRecipe($this);
        }

        return $this;
    }

    public function removeStep(Step $step): self
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getRecipe() === $this) {
                $step->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRecipe($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeRecipe($this);
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

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
