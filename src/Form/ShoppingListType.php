<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\ShoppingList;
use App\Repository\RecipeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingListType extends AbstractType
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
            ->add('name', TextType::class, [
                'label' => 'Nom de la liste',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Liste du week-end, Courses de la semaine...'
                ]
            ])
            ->add('recipes', EntityType::class, [
                'class' => Recipe::class,
                'multiple' => true, // permet à l'utilisateur de sélectionner plusieurs recettes
                'expanded' => true, // affiche les cases à cocher au lieu d'une liste déroulante
                'choice_label' => 'name', // champ utilisé pour afficher les options dans le formulaire
                'label' => 'Sélectionnez les recettes',
                'query_builder' => function (RecipeRepository $er) use ($user) {
                    return $er->createQueryBuilder('r')
                        ->where('r.user = :user')
                        ->setParameter('user', $user)
                        ->orderBy('r.name', 'ASC');
                },
                'attr' => [
                    'class' => 'recipe-list'
                ],
                'label_attr' => [
                    'class' => 'fw-bold mb-3'
                ]
            ])
            ->add('customItems', CollectionType::class, [
                'entry_type' => ShoppingListItemType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Articles libres',
                'attr' => [
                    'class' => 'custom-items-collection'
                ]
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
