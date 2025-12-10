<?php

namespace App\Form;

use App\Entity\RecipeIngredient;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecipeIngredientType extends AbstractType
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        $builder
            ->add('ingredient', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un ingrédient',
                'required' => true,
                'label' => false,
                'query_builder' => function (IngredientRepository $er) use ($user) {
                    return $er->createQueryBuilder('i')
                        ->where('i.user = :user')
                        ->setParameter('user', $user)
                        ->orderBy('i.name', 'ASC');
                },
                'row_attr' => [
                    'class' => 'mb-2'
                ]
            ])
            ->add('quantity', NumberType::class, [
                'attr' => [
                    'min' => 0,
                    'step' => 'any',
                    'placeholder' => 'Quantité'
                ],
                'label' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'mb-0'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
        ]);
    }
}
