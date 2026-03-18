<?php

declare(strict_types=1);

namespace App\Service;

use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Use this class only in context of HTTP requests.
 */
class UrlService
{
    protected Request $request;

    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly UrlHelper $urlHelper,
        protected readonly UrlGeneratorInterface $urlGenerator,
    ) {
        $request = $requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new RuntimeException('Request not available in context.');
        }

        $this->request = $request;
    }

    /**
     * @param array<int|string, int|string|null> $routeParams
     */
    public function generateUrlFromRoute(string $route, array $routeParams = []): string
    {
        return $this->urlGenerator->generate(
            $route,
            $routeParams,
            UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    /**
     * Canonical URLs are used for HTML meta tags rel=canonical and point to the prefered URL if multiple exist.
     * Often without query parameters.
     */
    public function generateCanonicalUrl(): string
    {
        // $this->request->query->all()
        return $this->generateUrlFromRoute(
            $this->getCurrentRoute(),
            $this->request->attributes->all('_route_params')
        );
    }

    private function getCurrentRoute(): string
    {
        return $this->request->attributes->get('_route') ?? 'index';
    }
}
