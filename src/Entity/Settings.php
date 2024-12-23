<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $resize_enabled = true;

    #[ORM\Column(type: 'integer', options: ['default' => 1920])]
    private int $max_width = 1920;

    #[ORM\Column(type: 'integer', options: ['default' => 1080])]
    private int $max_height = 1080;

    #[ORM\Column(type: 'integer', options: ['default' => 85])]
    private int $jpeg_quality = 85;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $webp_enabled = true;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $auto_alt_enabled = true;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $alt_format = '[filename] image';

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $grapejs_enabled = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $storage_type = 'local';

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $s3_endpoint = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $s3_public_url = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $s3_region = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $s3_bucket = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $s3_access_key = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $s3_secret_key = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $headless_mode = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isResizeEnabled(): bool
    {
        return $this->resize_enabled;
    }

    public function setResizeEnabled(bool $resize_enabled): self
    {
        $this->resize_enabled = $resize_enabled;
        return $this;
    }

    public function getMaxWidth(): int
    {
        return $this->max_width;
    }

    public function setMaxWidth(int $max_width): self
    {
        $this->max_width = $max_width;
        return $this;
    }

    public function getMaxHeight(): int
    {
        return $this->max_height;
    }

    public function setMaxHeight(int $max_height): self
    {
        $this->max_height = $max_height;
        return $this;
    }

    public function getJpegQuality(): int
    {
        return $this->jpeg_quality;
    }

    public function setJpegQuality(int $jpeg_quality): self
    {
        $this->jpeg_quality = $jpeg_quality;
        return $this;
    }

    public function isWebpEnabled(): bool
    {
        return $this->webp_enabled;
    }

    public function setWebpEnabled(bool $webp_enabled): self
    {
        $this->webp_enabled = $webp_enabled;
        return $this;
    }

    public function isAutoAltEnabled(): bool
    {
        return $this->auto_alt_enabled;
    }

    public function setAutoAltEnabled(bool $auto_alt_enabled): self
    {
        $this->auto_alt_enabled = $auto_alt_enabled;
        return $this;
    }

    public function getAltFormat(): ?string
    {
        return $this->alt_format;
    }

    public function setAltFormat(?string $alt_format): self
    {
        $this->alt_format = $alt_format;
        return $this;
    }

    public function isGrapejsEnabled(): bool
    {
        return $this->grapejs_enabled;
    }

    public function setGrapejsEnabled(bool $grapejs_enabled): self
    {
        $this->grapejs_enabled = $grapejs_enabled;
        return $this;
    }

    public function isHeadlessMode(): bool
    {
        return $this->headless_mode;
    }

    public function setHeadlessMode(bool $headless_mode): self
    {
        $this->headless_mode = $headless_mode;
        return $this;
    }

    public function getStorageType(): ?string
    {
        return $this->storage_type;
    }

    public function setStorageType(?string $storage_type): self
    {
        $this->storage_type = $storage_type;
        return $this;
    }

    public function getS3Endpoint(): ?string
    {
        return $this->s3_endpoint;
    }

    public function setS3Endpoint(?string $s3_endpoint): self
    {
        $this->s3_endpoint = $s3_endpoint;
        return $this;
    }

    public function getS3PublicUrl(): ?string
    {
        return $this->s3_public_url;
    }

    public function setS3PublicUrl(?string $s3_public_url): self
    {
        $this->s3_public_url = $s3_public_url;
        return $this;
    }

    public function getS3Region(): ?string
    {
        return $this->s3_region;
    }

    public function setS3Region(?string $s3_region): self
    {
        $this->s3_region = $s3_region;
        return $this;
    }

    public function getS3Bucket(): ?string
    {
        return $this->s3_bucket;
    }

    public function setS3Bucket(?string $s3_bucket): self
    {
        $this->s3_bucket = $s3_bucket;
        return $this;
    }

    public function getS3AccessKey(): ?string
    {
        return $this->s3_access_key;
    }

    public function setS3AccessKey(?string $s3_access_key): self
    {
        $this->s3_access_key = $s3_access_key;
        return $this;
    }

    public function getS3SecretKey(): ?string
    {
        return $this->s3_secret_key;
    }

    public function setS3SecretKey(?string $s3_secret_key): self
    {
        $this->s3_secret_key = $s3_secret_key;
        return $this;
    }
} 