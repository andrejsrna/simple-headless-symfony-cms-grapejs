<?php

namespace App\Controller;

use App\Service\S3StorageService;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/admin/external-images', name: 'admin_external_images_')]
#[IsGranted('ROLE_EDITOR')]
class AdminExternalImageController extends AbstractController
{
    public function __construct(
        private S3StorageService $s3StorageService,
        private SettingsRepository $settingsRepository
    ) {}

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $settings = $this->settingsRepository->getSettings('image_settings');
        
        if (!$settings || $settings->getStorageType() !== 's3') {
            $this->addFlash('error', 'S3 storage is not configured');
            return $this->redirectToRoute('admin_settings_index');
        }

        $page = max(1, $request->query->getInt('page', 1));
        $perPage = 12;
        $startAfter = $request->query->get('startAfter');

        try {
            $result = $this->s3StorageService->listFiles('', $perPage, $startAfter);
            $files = $result['files'];
            $hasMore = $result['hasMore'];
            $nextStartAfter = $result['nextStartAfter'];
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to list files: ' . $e->getMessage());
            $files = [];
            $hasMore = false;
            $nextStartAfter = null;
        }

        return $this->render('admin/external_image/index.html.twig', [
            'files' => $files,
            'currentPage' => $page,
            'hasMore' => $hasMore,
            'nextStartAfter' => $nextStartAfter,
            'headless_mode' => false,
        ]);
    }

    #[Route('/upload', name: 'upload', methods: ['POST'])]
    public function upload(Request $request): Response
    {
        $settings = $this->settingsRepository->getSettings('image_settings');
        
        if (!$settings || $settings->getStorageType() !== 's3') {
            throw $this->createNotFoundException('S3 storage is not configured');
        }

        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');

        if (!$file) {
            throw $this->createNotFoundException('No file uploaded');
        }

        try {
            $result = $this->s3StorageService->uploadFile($file);
            $this->addFlash('success', 'File uploaded successfully');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to upload file: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_external_images_index');
    }

    #[Route('/{key}/delete', name: 'delete', methods: ['POST'], requirements: ['key' => '.+'])]
    public function delete(string $key, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$key, $request->request->get('_token'))) {
            try {
                $this->s3StorageService->deleteFile($key);
                $this->addFlash('success', 'File deleted successfully');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Failed to delete file: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('admin_external_images_index');
    }

    #[Route('/update-alt/{key}', name: 'update_alt', methods: ['POST'], requirements: ['key' => '.+'])]
    public function updateAlt(string $key, Request $request): JsonResponse
    {
        try {
            $altText = $request->request->get('altText');
            $this->s3StorageService->updateMetadata($key, ['alt' => $altText]);
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
} 