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
} 