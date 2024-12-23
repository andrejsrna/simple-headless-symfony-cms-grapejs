<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use App\Service\ImageProcessor;
use App\Service\S3StorageService;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class ImageUploadSubscriber implements EventSubscriberInterface
{
    private Filesystem $filesystem;

    public function __construct(
        private ImageProcessor $imageProcessor,
        private EntityManagerInterface $entityManager,
        private SettingsRepository $settingsRepository,
        private S3StorageService $s3StorageService
    ) {
        $this->filesystem = new Filesystem();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::POST_UPLOAD => 'onPostUpload',
        ];
    }

    public function onPostUpload(Event $event): void
    {
        $object = $event->getObject();

        // Only process Article images
        if (!$object instanceof Article) {
            return;
        }

        // Get settings
        $settings = $this->settingsRepository->getSettings('image_settings');
        
        // Get the uploaded file
        $mapping = $event->getMapping();
        $filename = $object->getImageName();
        
        if (!$filename) {
            return;
        }

        try {
            // Get the full path of the uploaded file
            $uploadDir = $mapping->getUploadDestination();
            $originalFile = new File($uploadDir . '/' . $filename);
            
            // Process the image if not using S3
            if (!$settings || $settings->getStorageType() !== 's3') {
                $processedImage = $this->imageProcessor->processImage($originalFile);
                
                // Update the article with the new filename if it changed
                if ($processedImage->getFilename() !== $filename) {
                    $object->setImageName($processedImage->getFilename());
                    $this->entityManager->flush();

                    // Remove the original file
                    if ($this->filesystem->exists($originalFile->getPathname())) {
                        $this->filesystem->remove($originalFile->getPathname());
                    }
                    
                    // Update the file reference to the processed image
                    $originalFile = $processedImage;
                }
            }

            // If S3 storage is enabled, upload to S3 and clean up local file
            if ($settings && $settings->getStorageType() === 's3') {
                try {
                    // Upload to S3
                    $result = $this->s3StorageService->uploadFile($originalFile);
                    
                    // Update the article with the full S3 URL
                    $object->setImageName($result['path']);
                    $object->setStorageType('s3');
                    $this->entityManager->flush();
                    
                    // Remove the local file
                    if ($this->filesystem->exists($originalFile->getPathname())) {
                        $this->filesystem->remove($originalFile->getPathname());
                    }
                } catch (\Exception $e) {
                    // Log S3 upload error but don't throw - we still have the local file as fallback
                    error_log('Error uploading to S3: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // Log error or handle it appropriately
            error_log('Error processing image: ' . $e->getMessage());
        }
    }
} 