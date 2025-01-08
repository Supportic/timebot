<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Service\Misc\VersionManager;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class VersionExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        protected readonly VersionManager $versionManager
    ) {}

    public function getGlobals(): array
    {
        return [
            'app_version' => $this->versionManager->getVersion(),
        ];
    }
}
