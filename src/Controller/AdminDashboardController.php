<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use App\Repository\PageRepository;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use App\Service\S3StorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_EDITOR')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        ArticleRepository $articleRepository,
        PageRepository $pageRepository,
        UserRepository $userRepository,
        ImageRepository $imageRepository,
        S3StorageService $s3StorageService
    ): Response {
        // Get counts for dashboard stats
        $stats = [
            'articles' => [
                'count' => $articleRepository->count([]),
                'icon' => 'fas fa-newspaper',
                'label' => 'Articles',
                'link' => 'admin_articles_index'
            ],
            'pages' => [
                'count' => $pageRepository->count([]),
                'icon' => 'fas fa-file',
                'label' => 'Pages',
                'link' => 'admin_pages_index'
            ],
            'images' => [
                'count' => $imageRepository->count([]),
                'icon' => 'fas fa-images',
                'label' => 'Images',
                'link' => 'admin_images_index'
            ],
            'users' => [
                'count' => $userRepository->count([]),
                'icon' => 'fas fa-users',
                'label' => 'Users',
                'link' => 'admin_users_index'
            ]
        ];

        // Get recent articles
        $recentArticles = $articleRepository->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'recent_articles' => $recentArticles,
            's3_storage_service' => $s3StorageService
        ]);
    }
} 