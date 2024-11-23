<?php

namespace App\Entity\Enum;

use Cerbero\Enum\Concerns\Enumerates;
use Symfony\Component\Translation\TranslatableMessage;

enum RolesEnum: string
{
    use Enumerates;

    case ROLE_SUPER_ADMIN   = 'Super Admin';
    case ROLE_ADMIN         = 'Admin';
    case ROLE_MEMBER        = 'Member';
    case ROLE_USER          = 'User';

    public function trans(): string
    {
        return match ($this) {
            self::ROLE_SUPER_ADMIN  => new TranslatableMessage('roles.super_admin', domain: 'roles'),
            self::ROLE_ADMIN        => new TranslatableMessage('roles.admin', domain: 'roles'),
            self::ROLE_MEMBER       => new TranslatableMessage('roles.member', domain: 'roles'),
            self::ROLE_USER         => new TranslatableMessage('roles.user', domain: 'roles'),
        };
    }
}
