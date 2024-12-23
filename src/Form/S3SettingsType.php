<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class S3SettingsType extends AbstractType
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
                'help' => 'The endpoint URL for your S3-compatible storage (e.g., https://s3.amazonaws.com)',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
} 