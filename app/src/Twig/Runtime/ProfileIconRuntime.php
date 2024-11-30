<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\Markup;

class ProfileIconRuntime implements RuntimeExtensionInterface
{
    public function __construct(protected readonly Security $security) {}

    public function renderProfileIcon(
        string $name = '',
        string $class = '',
        string $textColor = '#3730a',
        string $backgroundColor = '#c7d2fe',
        bool $rounded = true,
        int $size = 64,
        int $fontSize = 28
    ): Markup {

        $acronymName = $this->createAcronymName($name);

        /** @var string[] */
        $classes = array_values(
            array_filter(
                preg_split('/\s/', $class),
                fn($value) => !is_null($value) && $value !== ''
            )
        );

        if ($rounded) {
            $classes[] = 'rounded-md';
        }

        $class = empty($classes) ? '' : ' class="' . implode(" ", $classes) . '"';

        $svg = '<svg' . $class . ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="' . $size . 'px" height="' . $size . 'px" viewBox="0 0 ' . $size . ' ' . $size . '" version="1.1" aria-hidden="true"><rect fill="' . $backgroundColor . '" cx="' . ($size / 2) . '" width="' . $size . '" height="' . $size . '" cy="' . ($size / 2) . '" r="' . ($size / 2) . '"/><text x="50%" y="50%" style="color: ' . $textColor . '; line-height: 1;font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\', \'Ubuntu\', \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;" alignment-baseline="middle" text-anchor="middle" font-size="' . $fontSize . '" font-weight="600" dy=".1em" dominant-baseline="middle" fill="' . $textColor . '">' . mb_strtoupper($acronymName) . '</text></svg>';

        return new Markup($svg, 'UTF-8');
    }

    private function createAcronymName(string $name): string
    {
        // trying to retrieve the user from the current session
        $user = $this->security->isGranted('IS_AUTHENTICATED_FULLY') ? $this->security->getUser() : null;

        if (empty($name) && $user instanceof User) {
            $name = $user->getUserIdentifier();
        }

        [$firstName, $lastName]  = array_pad(mb_split('/\s/', $name, 1), 2, '');

        if ($firstName && '' === $lastName) {
            return mb_strimwidth($name, 0, 2);
        } else if ($firstName && $lastName) {
            return mb_strimwidth($firstName, 0,  1) . mb_strimwidth($lastName, 0, 1);
        }

        return 'TB';
    }
}
