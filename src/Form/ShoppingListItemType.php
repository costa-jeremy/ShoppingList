<?php

namespace App\Form;

use App\Entity\ShoppingListItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShoppingListItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Article',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Lait, Pain, etc.'
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '1',
                    'step' => '0.01'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShoppingListItem::class,
        ]);
    }
}

