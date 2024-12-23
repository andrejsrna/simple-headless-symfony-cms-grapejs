<?php

namespace App\Controller;

use App\Entity\Settings;
use App\Form\ImageSettingsType;
use App\Form\AppearanceSettingsType;
use App\Form\GeneralSettingsType;
use App\Repository\SettingsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/settings')]
class AdminSettingsController extends AbstractController
{
    public function __construct(
        private SettingsRepository $settingsRepository,
        private UserRepository $userRepository
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

        // Get or create general settings
        $generalSettings = $this->settingsRepository->getSettings('general_settings');
        if (!$generalSettings) {
            $generalSettings = new Settings();
            $generalSettings->setName('general_settings');
            $generalSettings->setHeadlessMode(false);
        }

        $imageForm = $this->createForm(ImageSettingsType::class, $imageSettings);
        $appearanceForm = $this->createForm(AppearanceSettingsType::class, $appearanceSettings);
        $generalForm = $this->createForm(GeneralSettingsType::class, $generalSettings);

        $imageForm->handleRequest($request);
        $appearanceForm->handleRequest($request);
        $generalForm->handleRequest($request);

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

        if ($generalForm->isSubmitted() && $generalForm->isValid()) {
            try {
                $this->settingsRepository->saveSettings($generalSettings);
                $this->addFlash('success', 'General settings saved successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Failed to save settings: ' . $e->getMessage());
            }
            return $this->redirectToRoute('admin_settings_index');
        }

        return $this->render('admin/settings/index.html.twig', [
            'image_settings_form' => $imageForm->createView(),
            'appearance_settings_form' => $appearanceForm->createView(),
            'general_settings_form' => $generalForm->createView(),
            'users' => $this->userRepository->findAll(),
        ]);
    }
} 