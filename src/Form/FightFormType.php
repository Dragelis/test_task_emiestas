<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Fight;

class FightFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('participant', TextType::class, [
                'label' => 'Participant name'
            ])
            ->add('opponent', TextType::class, [
                'label' => 'Opponent name'
            ])
            ->add('startTime', DateTimeType::class, [
                'label' => 'Starts On'
            ])
            ->add('register', SubmitType::class, [
                'label' => 'Create'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fight::class
        ]);
    }
}
