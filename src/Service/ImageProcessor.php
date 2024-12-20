<?php

namespace App\Service;

use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\File\File;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;

class ImageProcessor
{
    private $imagine;
    private $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->imagine = new Imagine();
        $this->settingsRepository = $settingsRepository;
    }

    public function processImage(File $file): File
    {
        $settings = $this->settingsRepository->getSettings('image_settings') ?? [
            'resize_enabled' => true,
            'max_width' => 1920,
            'max_height' => 1080,
            'jpeg_quality' => 85,
            'webp_enabled' => true,
        ];

        // Get the original image
        $image = $this->imagine->open($file->getPathname());
        $originalSize = $image->getSize();

        // Resize if enabled and needed
        if ($settings['resize_enabled']) {
            $width = $originalSize->getWidth();
            $height = $originalSize->getHeight();
            $maxWidth = $settings['max_width'];
            $maxHeight = $settings['max_height'];

            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);

                $image = $image->resize(new Box($newWidth, $newHeight));
            }
        }

        // Convert to WebP if enabled
        if ($settings['webp_enabled']) {
            $webpPath = $file->getPath() . '/' . pathinfo($file->getFilename(), PATHINFO_FILENAME) . '.webp';
            $image->save($webpPath, [
                'webp_quality' => $settings['jpeg_quality']
            ]);

            // Return the WebP version
            return new File($webpPath);
        }

        // Save with JPEG quality if it's a JPEG
        if (in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'])) {
            $image->save($file->getPathname(), [
                'jpeg_quality' => $settings['jpeg_quality']
            ]);
        } else {
            $image->save($file->getPathname());
        }

        return $file;
    }
} 