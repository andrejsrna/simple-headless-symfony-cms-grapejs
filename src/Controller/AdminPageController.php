<?php

namespace App\Controller;

use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Settings;

#[Route('/admin/pages')]
class AdminPageController extends AbstractController
{
    public function __construct(
        private SettingsRepository $settingsRepository
    ) {}

    #[Route('/', name: 'admin_pages_index', methods: ['GET'])]
    public function index(PageRepository $pageRepository): Response
    {
        $settings = $this->settingsRepository->getSettings('general_settings');

        return $this->render('admin/page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
            'headless_mode' => $settings?->isHeadlessMode() ?? false,
        ]);
    }

    #[Route('/new', name: 'admin_pages_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $page = new Page();
        $settings = $this->settingsRepository->getSettings('appearance_settings');
        
        // Initialize settings if they don't exist
        if (!$settings) {
            $settings = new Settings();
            $settings->setName('appearance_settings');
            $settings->setGrapejsEnabled(false);
            $this->settingsRepository->saveSettings($settings);
        }
        
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $now = new \DateTimeImmutable();
            $page->setCreatedAtValue($now);
            $page->setUpdatedAt($now);
            
            $entityManager->persist($page);
            $entityManager->flush();

            $this->addFlash('success', 'Page created successfully.');
            return $this->redirectToRoute('admin_pages_index');
        }

        return $this->render('admin/page/new.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_pages_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        $settings = $this->settingsRepository->getSettings('appearance_settings');
        
        // Initialize settings if they don't exist
        if (!$settings) {
            $settings = new Settings();
            $settings->setName('appearance_settings');
            $settings->setGrapejsEnabled(false);
            $this->settingsRepository->saveSettings($settings);
        }
        
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', 'Page updated successfully.');
            return $this->redirectToRoute('admin_pages_index');
        }

        return $this->render('admin/page/edit.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'settings' => $settings,
        ]);
    }

    #[Route('/{id}', name: 'admin_pages_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $entityManager->remove($page);
            $entityManager->flush();
            $this->addFlash('success', 'Page deleted successfully.');
        }

        return $this->redirectToRoute('admin_pages_index');
    }
} 