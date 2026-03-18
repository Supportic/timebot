<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Seo\RouteDTO;

class SeoService
{
    /**
     * @param array<string, string> $metaNames
     * @param array<string, string> $metaProperties
     */
    public function __construct(
        private bool|string $title = true,
        private false|string $description = false,
        private array $metaNames = [],
        private array $metaProperties = [],
        private bool|RouteDTO $canonicalUrl = true
    ) {}

    public function getTitle(): bool|string
    {
        return $this->title;
    }

    public function setTitle(bool|string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): false|string
    {
        return $this->description;
    }

    public function setDescription(false|string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param array<string, string> $metaNames
     */
    public function setMetaNames(array $metaNames): self
    {
        $this->metaNames = $metaNames;

        return $this;
    }

    /**
     * @param array<string, string> $metaNames
     */
    public function addMetaNames(array $metaNames): self
    {
        $this->metaNames = array_merge($this->metaNames, $metaNames);

        return $this;
    }

    /**
     * @return array<string, string> $metaNames
     */
    public function getMetaNames(): array
    {
        return $this->metaNames;
    }

    /**
     * @param array<string, string> $metaProperties
     */
    public function setMetaProperties(array $metaProperties): self
    {
        $this->metaProperties = $metaProperties;

        return $this;
    }

    /**
     * @param array<string, string> $metaProperties
     */
    public function addMetaProperties(array $metaProperties): self
    {
        $this->metaProperties = array_merge($this->metaProperties, $metaProperties);

        return $this;
    }

    /**
     * @return array<string, string> $metaNames
     */
    public function getMetaProperties(): array
    {
        return $this->metaProperties;
    }

    public function setCanonicalUrl(bool|RouteDTO $canonicalUrl): self
    {
        $this->canonicalUrl = $canonicalUrl;

        return $this;
    }

    public function getCanonicalUrl(): bool|RouteDTO
    {
        return $this->canonicalUrl;
    }
}
