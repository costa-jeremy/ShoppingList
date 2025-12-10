<?php

namespace App\Form;

use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'ingrédient',
                'attr' => [
                    'placeholder' => 'Ex: Farine, Sucre, Lait...'
                ]
            ])
            ->add('unit', ChoiceType::class, [
                'label' => 'Unité de mesure',
                'choices' => [
                    'Grammes' => 'g',
                    'Millilitres' => 'ml',
                    'Pièces' => 'pcs',
                    'Cuillères à soupe' => 'càs',
                    'Cuillères à café' => 'càc',
                    'Kilogrammes' => 'kg',
                    'Litres' => 'L'
                ],
                'placeholder' => 'Choisir une unité'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
