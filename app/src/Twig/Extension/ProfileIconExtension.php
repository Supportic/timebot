<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\ProfileIconRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProfileIconExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            // put function in runtime file to lazy load function only when needed
            new TwigFunction('renderProfileIcon', [ProfileIconRuntime::class, 'renderProfileIcon']),
        ];
    }
}
