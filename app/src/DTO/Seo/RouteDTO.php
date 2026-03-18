<?php

declare(strict_types=1);

namespace App\DTO\Seo;

class RouteDTO
{
    /**
     * @param array<int|string, int|string|null> $routeParams
     */
    public function __construct(
        public readonly string $route,
        public readonly array $routeParams = [],
    ) {}
}
