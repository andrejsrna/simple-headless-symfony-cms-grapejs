<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class PageController extends AbstractController
{
    #[Route('/pages/{id}/content', name: 'admin_page_save_content', methods: ['POST'])]
    public function saveContent(Request $request, Page $page, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['content'])) {
                return new JsonResponse([
                    'error' => 'Content is required'
                ], Response::HTTP_BAD_REQUEST);
            }

            $content = $data['content'];
            $page->setContent($content);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Content saved successfully'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
} 