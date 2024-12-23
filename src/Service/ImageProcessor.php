<?php

namespace App\Service;

use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageProcessor
{
    private $imagine;
    private $settingsRepository;
    private $slugger;
    private $s3StorageService;
    private $filesystem;

    public function __construct(
        SettingsRepository $settingsRepository,
        SluggerInterface $slugger,
        S3StorageService $s3StorageService
    ) {
        $this->imagine = new Imagine();
        $this->settingsRepository = $settingsRepository;
        $this->slugger = $slugger;
        $this->s3StorageService = $s3StorageService;
        $this->filesystem = new Filesystem();
    }

    private function generateAltTag(string $filename): string
    {
        $settings = $this->settingsRepository->getSettings('image_settings');
        
        if (!$settings->isAutoImageAlt()) {
            return '';
        }

        // Remove extension and convert to readable format
        $baseFilename = pathinfo($filename, PATHINFO_FILENAME);
        $humanReadable = str_replace(['-', '_'], ' ', $baseFilename);
        
        $format = $settings->getAutoAltFormat() ?? '[filename] image';
        return str_replace('[filename]', $humanReadable, $format);
    }

    public function processImage(File $file): File
    {
        $settings = $this->settingsRepository->getSettings('image_settings');

        // Get the original image
        $image = $this->imagine->open($file->getPathname());
        $originalSize = $image->getSize();

        // Resize if enabled and needed
        if ($settings->isResizeEnabled()) {
            $width = $originalSize->getWidth();
            $height = $originalSize->getHeight();
            $maxWidth = $settings->getMaxWidth();
            $maxHeight = $settings->getMaxHeight();

            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);

                $image = $image->resize(new Box($newWidth, $newHeight));
            }
        }

        // Create a temporary file for the processed image
        $tempDir = sys_get_temp_dir();
        $tempFilename = uniqid('processed_') . '.' . $file->guessExtension();
        $tempPath = $tempDir . '/' . $tempFilename;

        // Convert to WebP if enabled
        if ($settings->isWebpEnabled()) {
            $tempWebpPath = $tempDir . '/' . pathinfo($tempFilename, PATHINFO_FILENAME) . '.webp';
            $image->save($tempWebpPath, [
                'webp_quality' => $settings->getJpegQuality()
            ]);
            $processedFile = new File($tempWebpPath);
        } else {
            // Save with JPEG quality if it's a JPEG
            if (in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'])) {
                $image->save($tempPath, [
                    'jpeg_quality' => $settings->getJpegQuality()
                ]);
            } else {
                $image->save($tempPath);
            }
            $processedFile = new File($tempPath);
        }

        // If S3 storage is enabled, upload to S3
        if ($settings->getStorageType() === 's3') {
            try {
                $result = $this->s3StorageService->uploadFile($processedFile);
                // Clean up temporary files
                $this->filesystem->remove($tempPath);
                if (isset($tempWebpPath)) {
                    $this->filesystem->remove($tempWebpPath);
                }
                // Return a File object that points to the S3 URL
                return new File($result['path']);
            } catch (\Exception $e) {
                throw new \RuntimeException('Failed to upload to S3: ' . $e->getMessage());
            }
        }

        // For local storage, move the processed file to the uploads directory
        $targetDir = $this->getUploadsDir();
        if (!$this->filesystem->exists($targetDir)) {
            $this->filesystem->mkdir($targetDir);
        }

        $newFilename = $this->slugger->slug(pathinfo($processedFile->getFilename(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $processedFile->guessExtension();
        $newPath = $targetDir . '/' . $newFilename;
        $this->filesystem->rename($processedFile->getPathname(), $newPath, true);

        return new File($newPath);
    }

    public function processUploadedImage(UploadedFile $file, string $targetDirectory): array
    {
        $settings = $this->settingsRepository->getSettings('image_settings');
        
        // Process the image
        $processedFile = $this->processImage($file);
        
        // Generate alt tag
        $altTag = $this->generateAltTag($file->getClientOriginalName());
        
        // If using S3, the processImage method already uploaded the file
        if ($settings->getStorageType() === 's3') {
            return [
                'filename' => basename($processedFile->getPathname()),
                'alt' => $altTag,
                'url' => $processedFile->getPathname(), // This will be the S3 URL
                'storage_type' => 's3'
            ];
        }
        
        return [
            'filename' => $processedFile->getFilename(),
            'alt' => $altTag,
            'path' => $processedFile->getPathname(),
            'storage_type' => 'local'
        ];
    }

    private function getUploadsDir(): string
    {
        return dirname(__DIR__, 2) . '/public/uploads/images';
    }
} 
