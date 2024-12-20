<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/admin/images', name: 'admin_images_')]
class AdminImageController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $finder = new Finder();
        $images = [];
        
        // Find all files in the uploads/articles directory
        if (is_dir($this->getUploadsDir())) {
            $finder->files()->in($this->getUploadsDir())->name('/\.(jpg|jpeg|png|gif|webp)$/i');
            
            foreach ($finder as $file) {
                $images[] = [
                    'name' => $file->getFilename(),
                    'path' => '/uploads/articles/' . $file->getFilename(),
                    'size' => $this->formatFileSize($file->getSize()),
                    'modified' => new \DateTime('@' . $file->getMTime())
                ];
            }
        }

        return $this->render('admin/image/index.html.twig', [
            'images' => $images
        ]);
    }

    #[Route('/delete/{filename}', name: 'delete', methods: ['POST'])]
    public function delete(string $filename, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filename, $request->request->get('_token'))) {
            $filesystem = new Filesystem();
            $filePath = $this->getUploadsDir() . '/' . $filename;
            
            if ($filesystem->exists($filePath)) {
                $filesystem->remove($filePath);
                $this->addFlash('success', 'Image deleted successfully.');
            }
        }

        return $this->redirectToRoute('admin_images_index');
    }

    private function getUploadsDir(): string
    {
        return $this->getParameter('kernel.project_dir') . '/public/uploads/articles';
    }

    private function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }
} 