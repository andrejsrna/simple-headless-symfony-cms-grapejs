<?php

namespace App\Controller;

use App\Entity\Page;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/editor')]
class GrapeJSEditorController extends AbstractController
{
    public function __construct(
        private SettingsRepository $settingsRepository
    ) {}

    #[Route('/page/{id}', name: 'grapejs_editor_page')]
    public function editPage(Page $page): Response
    {
        $settings = $this->settingsRepository->getSettings('appearance_settings');
        
        if (!$settings?->isGrapejsEnabled()) {
            throw $this->createAccessDeniedException('GrapeJS editor is not enabled.');
        }

        return $this->render('editor/page.html.twig', [
            'page' => $page,
        ]);
    }
} 