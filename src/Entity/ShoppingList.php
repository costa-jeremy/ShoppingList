<?php

namespace App\Entity;

use App\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ShoppingListRepository::class)]
class ShoppingList
{

    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Recipe::class, inversedBy: 'shoppingLists', cascade: ['persist'])]
    private Collection $recipes;

    #[ORM\OneToMany(mappedBy: 'shoppingList', targetEntity: ShoppingListItem::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $customItems;

    #[ORM\ManyToOne(inversedBy: 'shoppingLists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->customItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->addShoppingList($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            $recipe->removeShoppingList($this);
        }

        return $this;
    }

    public function getTotalIngredients(): array
    {
        $totalIngredients = [];

        foreach ($this->recipes as $recipe) {
            foreach ($recipe->getRecipeIngredients() as $recipeIngredient) {
                $ingredient = $recipeIngredient->getIngredient();
                $ingredientId = $ingredient->getId();

                if (!isset($totalIngredients[$ingredientId])) {
                    $totalIngredients[$ingredientId] = [
                        'ingredient' => $ingredient,
                        'quantity' => 0,
                        'unit' => $ingredient->getUnit()
                    ];
                }

                $totalIngredients[$ingredientId]['quantity'] += $recipeIngredient->getQuantity();
            }
        }

        return array_values($totalIngredients);
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, ShoppingListItem>
     */
    public function getCustomItems(): Collection
    {
        return $this->customItems;
    }

    public function addCustomItem(ShoppingListItem $customItem): static
    {
        if (!$this->customItems->contains($customItem)) {
            $this->customItems->add($customItem);
            $customItem->setShoppingList($this);
        }

        return $this;
    }

    public function removeCustomItem(ShoppingListItem $customItem): static
    {
        if ($this->customItems->removeElement($customItem)) {
            // set the owning side to null (unless already changed)
            if ($customItem->getShoppingList() === $this) {
                $customItem->setShoppingList(null);
            }
        }

        return $this;
    }
}
