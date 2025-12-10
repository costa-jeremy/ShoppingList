<?php

declare(strict_types=1);

namespace App\Twig\Extensions\Recipe;


use App\Repository\RecipeRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RecipeExtension extends AbstractExtension
{

    public function __construct(private RecipeRepository $recipeRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_get_number_of_recipe', [$this, 'getNumberOfRecipe']),
        ];
    }

    public function getNumberOfRecipe(): ?int
    {
        if($this->recipeRepository->count([])){
            return $this->recipeRepository->count([]);
        }
        return 0;
    }
}
