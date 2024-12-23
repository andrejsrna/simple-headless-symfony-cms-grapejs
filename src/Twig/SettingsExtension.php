<?php

namespace App\Twig;

use App\Repository\SettingsRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension
{
    public function __construct(
        private SettingsRepository $settingsRepository
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('settings_repository', [$this, 'getSettingsRepository']),
        ];
    }

    public function getSettingsRepository(): SettingsRepository
    {
        return $this->settingsRepository;
    }
} 