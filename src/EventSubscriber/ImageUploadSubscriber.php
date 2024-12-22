<?php

namespace App\EventSubscriber;

use App\Entity\Article;
use App\Service\ImageProcessor;
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
        private EntityManagerInterface $entityManager
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
            
            // Process the image
            $processedImage = $this->imageProcessor->processImage($originalFile);
            
            // If the image was converted to WebP or renamed
            if ($processedImage->getFilename() !== $filename) {
                // Update the article with the new filename
                $object->setImageName($processedImage->getFilename());
                $this->entityManager->flush();

                // Remove the original file
                if ($this->filesystem->exists($originalFile->getPathname())) {
                    $this->filesystem->remove($originalFile->getPathname());
                }
            }
        } catch (\Exception $e) {
            // Log error or handle it appropriately
            error_log('Error processing image: ' . $e->getMessage());
        }
    }
} 