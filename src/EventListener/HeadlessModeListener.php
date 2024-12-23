<?php

namespace App\EventListener;

use App\Repository\SettingsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HeadlessModeListener implements EventSubscriberInterface
{
    public function __construct(
        private SettingsRepository $settingsRepository,
        private Security $security,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 31], // Priority higher than the firewall
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Skip admin routes and login route
        if (str_starts_with($path, '/admin') || $path === '/login') {
            return;
        }

        // Get general settings
        $settings = $this->settingsRepository->getSettings('general_settings');
        if (!$settings || !$settings->isHeadlessMode()) {
            return;
        }

        // If headless mode is enabled and user is not authenticated, redirect to login
        if (!$this->security->getUser()) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_login')));
        }
    }
} 