<?php

namespace App\Service;

use App\Repository\SettingsRepository;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class S3StorageService
{
    private ?S3Client $s3Client = null;
    private ?string $bucket = null;
    private ?string $endpoint = null;
    private ?string $publicUrl = null;

    public function __construct(
        private SettingsRepository $settingsRepository,
        private SluggerInterface $slugger
    ) {}

    private function initializeS3Client(): void
    {
        if ($this->s3Client !== null) {
            return;
        }

        $settings = $this->settingsRepository->getSettings('image_settings');
        if (!$settings || $settings->getStorageType() !== 's3') {
            throw new \RuntimeException('S3 storage is not configured');
        }

        $this->bucket = $settings->getS3Bucket();
        $this->endpoint = $settings->getS3Endpoint();
        $this->publicUrl = $settings->getS3PublicUrl();
        
        $config = [
            'version' => 'latest',
            'region' => 'auto',
            'credentials' => [
                'key' => $settings->getS3AccessKey(),
                'secret' => $settings->getS3SecretKey(),
            ],
            'use_path_style_endpoint' => true,
        ];

        if ($this->endpoint) {
            $config['endpoint'] = $this->endpoint;
            $config['use_path_style_endpoint'] = true;
        }

        $this->s3Client = new S3Client($config);
    }

    public function uploadFile(File $file, ?string $filename = null): array
    {
        $this->initializeS3Client();

        try {
            if (!$filename) {
                if ($file instanceof UploadedFile) {
                    $originalFilename = $file->getClientOriginalName();
                } else {
                    $originalFilename = basename($file->getPathname());
                }
                $safeFilename = $this->slugger->slug(pathinfo($originalFilename, PATHINFO_FILENAME));
                $filename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
            }

            error_log('Uploading file to S3: ' . $filename);

            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $filename,
                'SourceFile' => $file->getRealPath(),
                'ContentType' => $file->getMimeType(),
                'ACL' => 'public-read',
            ]);

            $url = $this->getPublicUrl($filename);
            error_log('File uploaded successfully. URL: ' . $url);

            return [
                'path' => $url,
                'filename' => $filename
            ];
        } catch (\Exception $e) {
            error_log('Failed to upload file to S3: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteFile(string $filename): void
    {
        $this->initializeS3Client();

        $this->s3Client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $filename,
        ]);
    }

    public function getPublicUrl(string $key): string
    {
        $this->initializeS3Client();
        
        if ($this->publicUrl) {
            if (str_starts_with($key, $this->publicUrl)) {
                return $key;
            }
            return rtrim($this->publicUrl, '/') . '/' . $key;
        }
        
        // Fallback to standard S3 URL if not using R2
        return $this->s3Client->getObjectUrl($this->bucket, $key);
    }

    public function listFiles(string $prefix = '', int $maxKeys = 12, ?string $startAfter = null): array
    {
        $this->initializeS3Client();

        try {
            $params = [
                'Bucket' => $this->bucket,
                'MaxKeys' => $maxKeys * 2,
                'Prefix' => $prefix,
            ];

            if ($startAfter) {
                $params['StartAfter'] = $startAfter;
            }

            // Use ListObjectsV2 with proper parameters
            $result = $this->s3Client->listObjectsV2([
                'Bucket' => $this->bucket,
                'MaxKeys' => 1000, // Get more objects to ensure we don't miss any
                'Prefix' => $prefix,
            ]);
            
            $files = [];
            if (!isset($result['Contents'])) {
                error_log('No contents found in S3 bucket: ' . $this->bucket);
                return [
                    'files' => [],
                    'hasMore' => false,
                    'nextStartAfter' => null,
                ];
            }

            foreach ($result['Contents'] as $object) {
                $metadata = $this->getObjectMetadata($object['Key']);
                $files[] = [
                    'key' => $object['Key'],
                    'size' => $object['Size'],
                    'modified' => $object['LastModified'],
                    'url' => $this->getPublicUrl($object['Key']),
                    'alt' => $metadata['alt'] ?? $object['Key'],
                ];
                error_log('Found file: ' . $object['Key'] . ' modified at: ' . $object['LastModified']->format('Y-m-d H:i:s'));
            }

            // Sort by modified date in descending order (newest first)
            usort($files, function($a, $b) {
                return $b['modified']->getTimestamp() - $a['modified']->getTimestamp();
            });

            // Take only the requested number of items
            $files = array_slice($files, 0, $maxKeys);

            return [
                'files' => $files,
                'hasMore' => count($result['Contents']) > $maxKeys,
                'nextStartAfter' => end($files)['key'] ?? null,
            ];
        } catch (\Exception $e) {
            error_log('S3 listing error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to list files from S3: ' . $e->getMessage());
        }
    }

    public function updateMetadata(string $key, array $metadata): void
    {
        $this->initializeS3Client();

        try {
            // Get the current object
            $result = $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);

            // Copy the object to itself with new metadata
            $this->s3Client->copyObject([
                'Bucket' => $this->bucket,
                'CopySource' => urlencode($this->bucket . '/' . $key),
                'Key' => $key,
                'Metadata' => array_merge($result['Metadata'] ?? [], $metadata),
                'MetadataDirective' => 'REPLACE',
                'ACL' => 'public-read',
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to update metadata: ' . $e->getMessage());
        }
    }

    private function getObjectMetadata(string $key): array
    {
        try {
            $result = $this->s3Client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);

            return $result['Metadata'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
} 