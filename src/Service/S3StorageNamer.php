<?php

namespace App\Service;

use App\Repository\SettingsRepository;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Symfony\Component\String\Slugger\SluggerInterface;

class S3StorageNamer implements NamerInterface
{
    public function __construct(
        private SettingsRepository $settingsRepository,
        private SluggerInterface $slugger,
        private S3StorageService $s3StorageService
    ) {}

    public function name($object, PropertyMapping $mapping): string
    {
        $settings = $this->settingsRepository->getSettings('image_settings');
        $file = $mapping->getFile($object);
        $originalName = $file->getClientOriginalName();
        $safeFilename = $this->slugger->slug(pathinfo($originalName, PATHINFO_FILENAME));
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        
        // If S3 storage is enabled, upload the file to S3
        if ($settings->getStorageType() === 's3') {
            try {
                $result = $this->s3StorageService->uploadFile($file, $newFilename);
                $object->setStorageType('s3');
                return $newFilename;
            } catch (\Exception $e) {
                throw new \RuntimeException('Failed to upload to S3: ' . $e->getMessage());
            }
        }

        // For local storage
        $object->setStorageType('local');
        return $newFilename;
    }
} 