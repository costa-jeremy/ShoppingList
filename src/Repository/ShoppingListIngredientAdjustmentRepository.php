<?php

namespace App\Repository;

use App\Entity\ShoppingListIngredientAdjustment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ShoppingListIngredientAdjustmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShoppingListIngredientAdjustment::class);
    }
}

