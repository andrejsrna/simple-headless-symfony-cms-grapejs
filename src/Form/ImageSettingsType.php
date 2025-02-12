<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('storage_type', ChoiceType::class, [
                'required' => true,
                'label' => 'Storage Type',
                'choices' => [
                    'Local Storage' => 'local',
                    'S3 Compatible Storage' => 's3',
                ],
            ])
            ->add('s3_endpoint', TextType::class, [
                'required' => false,
                'label' => 'S3 Endpoint',
                'help' => 'The S3 API endpoint (e.g., https://xxx.r2.cloudflarestorage.com)',
            ])
            ->add('s3_public_url', TextType::class, [
                'required' => false,
                'label' => 'S3 Public URL',
                'help' => 'The public URL for accessing files (e.g., https://pub-xxx.r2.dev)',
            ])
            ->add('s3_region', TextType::class, [
                'required' => false,
                'label' => 'S3 Region',
                'help' => 'The region where your bucket is located (e.g., us-east-1)',
            ])
            ->add('s3_bucket', TextType::class, [
                'required' => false,
                'label' => 'S3 Bucket',
                'help' => 'The name of your S3 bucket',
            ])
            ->add('s3_access_key', TextType::class, [
                'required' => false,
                'label' => 'S3 Access Key',
                'help' => 'Your S3 access key ID',
            ])
            ->add('s3_secret_key', TextType::class, [
                'required' => false,
                'label' => 'S3 Secret Key',
                'help' => 'Your S3 secret access key',
            ])
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