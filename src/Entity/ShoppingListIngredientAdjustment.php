<?php

namespace App\Entity;

use App\Repository\ShoppingListIngredientAdjustmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListIngredientAdjustmentRepository::class)]
#[ORM\UniqueConstraint(name: 'shopping_list_ingredient_unique', columns: ['shopping_list_id', 'ingredient_id'])]
class ShoppingListIngredientAdjustment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ShoppingList::class, inversedBy: 'ingredientAdjustments')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ShoppingList $shoppingList = null;

    #[ORM\ManyToOne(targetEntity: Ingredient::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Ingredient $ingredient = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $adjustedQuantity = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isHidden = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isInCart = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): self
    {
        $this->shoppingList = $shoppingList;
        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): self
    {
        $this->ingredient = $ingredient;
        return $this;
    }

    public function getAdjustedQuantity(): ?float
    {
        return $this->adjustedQuantity;
    }

    public function setAdjustedQuantity(?float $adjustedQuantity): self
    {
        $this->adjustedQuantity = $adjustedQuantity;
        return $this;
    }

    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;
        return $this;
    }

    public function isInCart(): bool
    {
        return $this->isInCart;
    }

    public function setIsInCart(bool $isInCart): self
    {
        $this->isInCart = $isInCart;
        return $this;
    }
}
