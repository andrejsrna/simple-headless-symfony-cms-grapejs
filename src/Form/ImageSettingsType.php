<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('resize_enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'Enable Image Resizing',
            ])
            ->add('max_width', NumberType::class, [
                'required' => false,
                'label' => 'Maximum Width',
            ])
            ->add('max_height', NumberType::class, [
                'required' => false,
                'label' => 'Maximum Height',
            ])
            ->add('jpeg_quality', NumberType::class, [
                'required' => false,
                'label' => 'JPEG Quality',
            ])
            ->add('webp_enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'Enable WebP Conversion',
            ])
            ->add('auto_alt_enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'Enable Automatic Alt Tags',
            ])
            ->add('alt_format', TextType::class, [
                'required' => false,
                'label' => 'Alt Tag Format',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
} 