<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints\File;

class FightsImportFormType extends AbstractType
{
    private const ALLOW_MIME_TYPES = [
        'text/csv',
        'text/plain'
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => self::ALLOW_MIME_TYPES
                    ])
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Import File']);
    }
}
