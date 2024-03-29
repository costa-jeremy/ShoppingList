<?php
namespace App\EntityListener;

use App\Entity\Recipe;

class RecipeListener
{
    public function prePersist(Recipe $recipe): void
    {
        if ($recipe->getNumberOfTimes() === null) {
            $recipe->setNumberOfTimes(0);
        }
    }
}
