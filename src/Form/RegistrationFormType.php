<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Validator\Constraints\IsTrue;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\User;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,

                'type' => PasswordType::class,

                'invalid_message' => 'The password fields must match.',

                'first_options' => [
                    'label' => 'Password',

                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password.'
                        ]),
    
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Your password should be at least {{ limit }} characters.',
                            'max' => 4096
                        ]),
    
                        new NotCompromisedPassword()
                    ]
                ],

                'second_options' => [
                    'label' => 'Repeat password'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,

                'label' => 'I have read and agree to the terms of service.',

                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.'
                    ])
                ]
            ])
            ->add('register', SubmitType::class, ['label' => 'Register']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
