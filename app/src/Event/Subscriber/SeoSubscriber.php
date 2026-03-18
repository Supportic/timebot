<?php

declare(strict_types=1);

namespace App\Event\Subscriber;

use App\Component\Routing\Attributes\Seo;
use App\Service\SeoService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SeoSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SeoService $seoService,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $controller = $event->getController();
        // Handle both standard controllers and invokable controllers
        $reflection = match (true) {
            is_array($controller) => new \ReflectionMethod(...$controller),
            is_object($controller) => new \ReflectionMethod($controller, '__invoke'),
            default => null,
        };

        if (!$reflection instanceof \ReflectionMethod) {
            return;
        }

        $attributes = $reflection->getAttributes(Seo::class);

        if ([] === $attributes) {
            return;
        }

        /** @var Seo $seoAttribute */
        $seoAttribute = $attributes[0]->newInstance();

        $this->seoService->setTitle($seoAttribute->title);
        $this->seoService->setDescription($seoAttribute->description);
        $this->seoService->addMetaNames($seoAttribute->metaNames);
        $this->seoService->addMetaProperties($seoAttribute->metaProperties);
        $this->seoService->setCanonicalUrl($seoAttribute->canonicalUrl);
    }
}
