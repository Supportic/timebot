<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Service\Misc\VersionManager;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

final class VersionExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly VersionManager $versionManager
    ) {}

    public function getGlobals(): array
    {
        return [
            'app_version' =>  $this->versionManager->getVersion()->toString(),
            'app_version_short' =>  $this->versionManager->getVersionShort()->toString(),
        ];
    }
}
