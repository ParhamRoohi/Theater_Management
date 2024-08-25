<?php

namespace App\Form;

use App\Entity\Theater;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class TheaterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.name',
            ])
            ->add('location', TextType::class, [
                'label' => 'form.location',
            ])
            ->add('capacity', IntegerType::class, [
                'label' => 'form.capacity',
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'form.date',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'form.description',
                'required' => false,
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'USD',
                'label' => 'form.ticket_price'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Theater::class,
        ]);
    }
}