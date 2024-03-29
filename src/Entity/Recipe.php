<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastTimeDone = null;

    #[ORM\Column]
    private ?int $numberOfTimes = 0;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    private ?ShoppingList $shoppingList = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }



    public function getNumberOfTimes(): ?int
    {
        return $this->numberOfTimes;
    }

    public function setNumberOfTimes(int $numberOfTimes): static
    {
        $this->numberOfTimes = $numberOfTimes;

        return $this;
    }

    public function getLastTimeDone(): ?\DateTimeInterface
    {
        return $this->lastTimeDone;
    }

    public function setLastTimeDone(?\DateTimeInterface $lastTimeDone): void
    {
        $this->lastTimeDone = $lastTimeDone;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): static
    {
        $this->shoppingList = $shoppingList;

        return $this;
    }

}
