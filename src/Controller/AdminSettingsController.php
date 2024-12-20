<?php

namespace App\Controller;

use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

#[Route('/admin/settings', name: 'admin_settings_')]
#[IsGranted('ROLE_ADMIN')]
class AdminSettingsController extends AbstractController
{
    public function __construct(
        private SettingsRepository $settingsRepository
    ) {}

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $imageSettings = $this->settingsRepository->getSettings('image_settings') ?? [
            'resize_enabled' => true,
            'max_width' => 1920,
            'max_height' => 1080,
            'jpeg_quality' => 85,
            'webp_enabled' => true,
        ];

        $form = $this->createFormBuilder($imageSettings)
            ->add('resize_enabled', CheckboxType::class, [
                'label' => 'Enable Image Resizing',
                'required' => false,
            ])
            ->add('max_width', IntegerType::class, [
                'label' => 'Maximum Width (pixels)',
                'attr' => [
                    'min' => 100,
                    'max' => 4096,
                ],
            ])
            ->add('max_height', IntegerType::class, [
                'label' => 'Maximum Height (pixels)',
                'attr' => [
                    'min' => 100,
                    'max' => 4096,
                ],
            ])
            ->add('jpeg_quality', IntegerType::class, [
                'label' => 'JPEG Quality (%)',
                'attr' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ])
            ->add('webp_enabled', CheckboxType::class, [
                'label' => 'Enable WebP Conversion',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Settings',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settings = $form->getData();
            $this->settingsRepository->saveSettings('image_settings', $settings);
            $this->addFlash('success', 'Image settings have been updated successfully.');

            return $this->redirectToRoute('admin_settings_index');
        }

        return $this->render('admin/settings/index.html.twig', [
            'image_settings_form' => $form->createView(),
        ]);
    }
} 