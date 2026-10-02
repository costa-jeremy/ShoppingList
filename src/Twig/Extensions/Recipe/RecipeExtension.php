<?php

declare(strict_types=1);

namespace App\Twig\Extensions\Recipe;


use App\Repository\RecipeRepository;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RecipeExtension extends AbstractExtension
{

    public function __construct(private RecipeRepository $recipeRepository, private Security $security)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_get_number_of_recipe', [$this, 'getNumberOfRecipe']),
        ];
    }

    public function getNumberOfRecipe(): int
    {
        $user = $this->security->getUser();
        if (!$user) {
            return 0;
        }

        return $this->recipeRepository->count(['user' => $user]);
    }
}
