<?php

namespace App\Security\Voter;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\ShoppingList;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Autorise l'accès à une recette, un ingrédient ou une liste de courses
 * uniquement à l'utilisateur qui en est propriétaire.
 */
class OwnerVoter extends Voter
{
    public const OWNER = 'OWNER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::OWNER
            && ($subject instanceof Recipe || $subject instanceof Ingredient || $subject instanceof ShoppingList);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $owner = $subject->getUser();

        return $owner !== null && $owner->getId() === $user->getId();
    }
}
