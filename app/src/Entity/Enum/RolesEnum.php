<?php

namespace App\Entity\Enum;

use Cerbero\Enum\Concerns\Enumerates;

/**
 * access in php: RolesEnum::ROLE_ADMIN->value
 * access in twig value: enum('App\\Entity\\Enum\\RolesEnum').ROLE_ADMIN.value
 * access in twig trans key: user.role.toTransKey
 */
enum RolesEnum: string
{
    // https://github.com/cerbero90/enum
    use Enumerates;

    case ROLE_SUPER_ADMIN   = 'ROLE_SUPER_ADMIN';
    case ROLE_ADMIN         = 'ROLE_ADMIN';
    case ROLE_MEMBER        = 'ROLE_MEMBER';
    case ROLE_USER          = 'ROLE_USER';
    case ROLE_API           = 'ROLE_API';

    public function toTransKey(): string
    {
        return match ($this) {
            self::ROLE_SUPER_ADMIN  => 'roles.super_admin',
            self::ROLE_ADMIN        => 'roles.admin',
            self::ROLE_MEMBER       => 'roles.member',
            self::ROLE_USER         => 'roles.user',
            self::ROLE_API          => 'roles.api',
        };
    }
}
