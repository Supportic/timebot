<?php

declare(strict_types=1);

namespace App\Twig\Component\Table;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

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
}
