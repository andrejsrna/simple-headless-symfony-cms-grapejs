<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Repository\SettingsRepository;
use App\Service\S3StorageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use App\Service\ImageProcessor;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/images', name: 'admin_images_')]
#[IsGranted('ROLE_EDITOR')]
class AdminImageController extends AbstractController
{
    public function __construct(
        private ImageProcessor $imageProcessor,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
        private ImageRepository $imageRepository,
        private SettingsRepository $settingsRepository,
        private S3StorageService $s3StorageService
    ) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $settings = $this->settingsRepository->getSettings('image_settings');
        $images = [];
        
        if ($settings && $settings->getStorageType() === 's3') {
            // List images from S3
            $result = $this->s3StorageService->listFiles();
            foreach ($result['files'] as $file) {
                $imageEntity = $this->imageRepository->findOneByFilename($file['key']);
                $images[] = [
                    'name' => $file['key'],
                    'path' => $file['url'],
                    'size' => $this->formatFileSize($file['size']),
                    'modified' => $file['modified'],
                    'altText' => $imageEntity ? $imageEntity->getAltText() : null,
                    'storage_type' => 's3'
                ];
            }
        } else {
            // List local images
            $finder = new Finder();
            if (is_dir($this->getUploadsDir())) {
                $finder->files()->in($this->getUploadsDir())->name('/\.(jpg|jpeg|png|gif|webp)$/i');
                
                foreach ($finder as $file) {
                    $filename = $file->getFilename();
                    $imageEntity = $this->imageRepository->findOneByFilename($filename);
                    
                    $images[] = [
                        'name' => $filename,
                        'path' => '/uploads/articles/' . $filename,
                        'size' => $this->formatFileSize($file->getSize()),
                        'modified' => new \DateTime('@' . $file->getMTime()),
                        'altText' => $imageEntity ? $imageEntity->getAltText() : null,
                        'storage_type' => 'local'
                    ];
                }
            }
        }

        return $this->render('admin/image/index.html.twig', [
            'images' => $images,
            'storage_type' => $settings?->getStorageType() ?? 'local'
        ]);
    }

    #[Route('/update-alt/{filename}', name: 'update_alt', methods: ['POST'])]
    public function updateAlt(string $filename, Request $request): JsonResponse
    {
        $settings = $this->settingsRepository->getSettings('image_settings');
        $altText = $request->request->get('altText');
        
        if ($settings && $settings->getStorageType() === 's3') {
            // Update S3 metadata
            $this->s3StorageService->updateMetadata($filename, ['alt' => $altText]);
        }
        
        $image = $this->imageRepository->findOneByFilename($filename);
        if (!$image) {
            $image = new Image();
            $image->setFilename($filename);
        }
        
        $image->setAltText($altText);
        $this->entityManager->persist($image);
        $this->entityManager->flush();
        
        return new JsonResponse(['success' => true]);
    }

    #[Route('/upload', name: 'upload', methods: ['POST'])]
    public function upload(Request $request): Response
    {
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('image');

        if (!$uploadedFile) {
            $this->addFlash('error', 'No file was uploaded.');
            return $this->redirectToRoute('admin_images_index');
        }

        // Validate the file
        $constraints = new Assert\Collection([
            'file' => [
                new Assert\NotBlank(),
                new Assert\File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image (JPEG, PNG, WEBP)',
                ])
            ]
        ]);

        $violations = $this->validator->validate(['file' => $uploadedFile], $constraints);

        if (count($violations) > 0) {
            $this->addFlash('error', $violations->get(0)->getMessage());
            return $this->redirectToRoute('admin_images_index');
        }

        try {
            $settings = $this->settingsRepository->getSettings('image_settings');
            
            if ($settings && $settings->getStorageType() === 's3') {
                // Upload directly to S3
                $result = $this->s3StorageService->uploadFile($uploadedFile);
                
                // Create Image entity
                $image = new Image();
                $image->setFilename($result['filename']);
                $this->entityManager->persist($image);
                $this->entityManager->flush();
                
                $this->addFlash('success', 'Image uploaded successfully to S3.');
            } else {
                // Create uploads directory if it doesn't exist
                $filesystem = new Filesystem();
                $uploadDir = $this->getUploadsDir();
                if (!$filesystem->exists($uploadDir)) {
                    $filesystem->mkdir($uploadDir);
                }

                // Generate a unique filename
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                // Move the file to the uploads directory
                $uploadedFile->move($uploadDir, $newFilename);

                // Process the image (resize and convert to WebP if enabled)
                $file = new File($uploadDir . '/' . $newFilename);
                $processedImage = $this->imageProcessor->processImage($file);
                
                // Create Image entity
                $image = new Image();
                $image->setFilename($processedImage->getFilename());
                $this->entityManager->persist($image);
                $this->entityManager->flush();

                // Remove the original file if it was converted to WebP
                if ($processedImage->getFilename() !== $newFilename) {
                    $filesystem->remove($file->getPathname());
                }

                $this->addFlash('success', 'Image uploaded successfully.');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error uploading image: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_images_index');
    }

    #[Route('/delete/{filename}', name: 'delete', methods: ['POST'], requirements: ['filename' => '.+'])]
    public function delete(string $filename, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$filename, $request->request->get('_token'))) {
            $settings = $this->settingsRepository->getSettings('image_settings');
            
            if ($settings && $settings->getStorageType() === 's3') {
                // Delete from S3
                $this->s3StorageService->deleteFile($filename);
            } else {
                // Delete local file
                $filesystem = new Filesystem();
                $filePath = $this->getUploadsDir() . '/' . $filename;
                
                if ($filesystem->exists($filePath)) {
                    $filesystem->remove($filePath);
                }
            }
            
            // Delete the Image entity if it exists
            $image = $this->imageRepository->findOneByFilename($filename);
            if ($image) {
                $this->entityManager->remove($image);
                $this->entityManager->flush();
            }
            
            $this->addFlash('success', 'Image deleted successfully.');
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