<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Form\ImageSettingsType;
use App\Form\AppearanceSettingsType;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/settings')]
class AdminSettingsController extends AbstractController
{
    public function __construct(
        private SettingsRepository $settingsRepository
    ) {}

    #[Route('/', name: 'admin_settings_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        // Get or create image settings
        $imageSettings = $this->settingsRepository->getSettings('image_settings');
        if (!$imageSettings) {
            $imageSettings = new Settings();
            $imageSettings->setName('image_settings');
            $imageSettings->setResizeEnabled(true);
            $imageSettings->setMaxWidth(1920);
            $imageSettings->setMaxHeight(1080);
            $imageSettings->setJpegQuality(85);
            $imageSettings->setWebpEnabled(true);
            $imageSettings->setAutoAltEnabled(true);
            $imageSettings->setAltFormat('[filename] image');
        }

        // Get or create appearance settings
        $appearanceSettings = $this->settingsRepository->getSettings('appearance_settings');
        if (!$appearanceSettings) {
            $appearanceSettings = new Settings();
            $appearanceSettings->setName('appearance_settings');
            $appearanceSettings->setGrapejsEnabled(false);
        }

        $imageForm = $this->createForm(ImageSettingsType::class, $imageSettings);
        $appearanceForm = $this->createForm(AppearanceSettingsType::class, $appearanceSettings);

        $imageForm->handleRequest($request);
        $appearanceForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            try {
                $this->settingsRepository->saveSettings($imageSettings);
                $this->addFlash('success', 'Image settings saved successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Failed to save settings: ' . $e->getMessage());
            }
            return $this->redirectToRoute('admin_settings_index');
        }

        if ($appearanceForm->isSubmitted() && $appearanceForm->isValid()) {
            try {
                $this->settingsRepository->saveSettings($appearanceSettings);
                $this->addFlash('success', 'Appearance settings saved successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Failed to save settings: ' . $e->getMessage());
            }
            return $this->redirectToRoute('admin_settings_index');
        }

        return $this->render('admin/settings/index.html.twig', [
            'image_settings_form' => $imageForm->createView(),
            'appearance_settings_form' => $appearanceForm->createView(),
        ]);
    }
} 