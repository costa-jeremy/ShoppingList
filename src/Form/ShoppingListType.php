<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\ShoppingList;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('recipes', EntityType::class, [
            'class' => Recipe::class,
            'multiple' => true, // permet à l'utilisateur de sélectionner plusieurs recettes
            'expanded' => true, // affiche les cases à cocher au lieu d'une liste déroulante
            'choice_label' => 'name', // champ utilisé pour afficher les options dans le formulaire
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShoppingList::class,
        ]);
    }
}
