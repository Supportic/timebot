<?php

namespace App\Enum;

use Cerbero\Enum\Concerns\Enumerates;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * access in php: Role::ROLE_ADMIN->value
 * access in twig value: enum('App\\Enum\\Role').ROLE_ADMIN.value
 */
enum UserState: string implements TranslatableInterface
{
    // https://github.com/cerbero90/enum
    use Enumerates;

    case ENABLED        = 'enabled';
    case DISABLED       = 'disabled';
    case PENDING        = 'pending';

    /**
     * Use in twig: {{ state|trans }}
     */
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::ENABLED       => $translator->trans('user_state.enabled', [], 'user_state', $locale),
            self::DISABLED      => $translator->trans('user_state.disabled', [], 'user_state', $locale),
            self::PENDING       => $translator->trans('user_state.pending', [], 'user_state', $locale),
        };
    }

    /**
     * TailwindCSS default color names
     * https://tailwindcss.com/docs/colors
     */
    public function color(): string
    {
        return match ($this) {
            self::ENABLED       => 'green',
            self::DISABLED      => 'red',
            self::PENDING       => 'yellow',
        };
    }
}
