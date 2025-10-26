<?php

namespace App\Enum;

use Cerbero\Enum\Concerns\Enumerates;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * access in php: Role::ROLE_ADMIN->value
 * access in twig value: enum('App\\Enum\\Role').ROLE_ADMIN.value
 */
enum Role: string implements TranslatableInterface
{
    // https://github.com/cerbero90/enum
    use Enumerates;

    case ROLE_SUPER_ADMIN   = 'ROLE_SUPER_ADMIN';
    case ROLE_ADMIN         = 'ROLE_ADMIN';
    case ROLE_MEMBER        = 'ROLE_MEMBER';
    case ROLE_USER          = 'ROLE_USER';
    case ROLE_API           = 'ROLE_API';

    /**
     * Use in twig: {{ role|trans }}
     */
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::ROLE_SUPER_ADMIN  => $translator->trans('roles.super_admin', [], 'roles', $locale),
            self::ROLE_ADMIN        => $translator->trans('roles.admin', [], 'roles', $locale),
            self::ROLE_MEMBER       => $translator->trans('roles.member', [], 'roles', $locale),
            self::ROLE_USER         => $translator->trans('roles.user', [], 'roles', $locale),
            self::ROLE_API          => $translator->trans('roles.api', [], 'roles', $locale),
        };
    }
}
