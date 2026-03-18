<?php

declare(strict_types=1);

namespace App\Component\Routing\Attributes;

use App\DTO\Seo\RouteDTO;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS)]
class Seo
{
    /**
     * @param array<string, string> $metaNames
     * @param array<string, string> $metaProperties
     */
    public function __construct(
        public bool|string $title = true,
        public false|string $description = false,
        public array $metaNames = [],
        public array $metaProperties = [],
        public bool|RouteDTO $canonicalUrl = true,
    ) {}
}
