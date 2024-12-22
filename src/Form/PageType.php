<?php

namespace App\Form;

use App\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Repository\SettingsRepository;

class PageType extends AbstractType
{
    public function __construct(
        private SettingsRepository $settingsRepository
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $settings = $this->settingsRepository->getSettings('image_settings');
        $grapejsEnabled = $settings?->isGrapejsEnabled() ?? false;

        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'attr' => [
                    'class' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    'placeholder' => 'Enter page title'
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'data-controller' => 'suneditor',
                    'rows' => 10,
                    'class' => 'hidden-if-grapejs',
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'The content cannot be empty',
                    ]),
                ],
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'Meta Title',
                'required' => false,
                'attr' => [
                    'class' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    'placeholder' => 'Enter meta title for SEO'
                ]
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Meta Description',
                'required' => false,
                'attr' => [
                    'class' => 'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    'placeholder' => 'Enter meta description for SEO',
                    'rows' => 3
                ]
            ])
            ->add('metaKeywords', TextType::class, [
                'label' => 'Meta Keywords',
                'required' => false,
                'attr' => [
                    'class' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    'placeholder' => 'Enter meta keywords (comma-separated)'
                ]
            ])
            ->add('slug', TextType::class, [
                'label' => 'URL Slug',
                'required' => false,
                'attr' => [
                    'class' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
                    'placeholder' => 'Enter URL slug (e.g., about-us)'
                ]
            ])
            ->add('isPublished', CheckboxType::class, [
                'required' => false,
                'label' => 'Publish',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
} 