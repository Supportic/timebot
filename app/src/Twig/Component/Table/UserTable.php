<?php

declare(strict_types=1);

namespace App\Twig\Component\Table;

use App\Entity\User;
use App\Enum\Role;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

/**
 * @extends BaseTable<User>
 */
#[AsLiveComponent(exposePublicProps: false)]
class UserTable extends BaseTable
{
    public function __construct(
        protected UserRepository $userRepository,
    ) {}

    /**
     * @return ServiceEntityRepository<User>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->userRepository;
    }

    /**
     * Define which User fields are searchable.
     * @return string[]
     */
    protected function getSearchableFields(): array
    {
        return ['id', 'username'];
    }

    /**
     * Define which User fields can be sorted.
     * @return array<int|string, string>
     */
    protected function getSortableFields(): array
    {
        return [
            'id',
            'username',
            'role' => 'roles' // Map frontend 'role' to Doctrine 'roles'
        ];
    }
}
