<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getGroups(): array { return ['test']; }

    /**
     * @var array<int, array{
     *  username: string,
     *  password: string,
     *  roles: string[]
     * }>
     */
    private array $users = [
        [
            'username' => 'superadmin',
            'password' => 'superadmin',
            'roles' => ['ROLE_SUPER_ADMIN'],
        ],
        [
            'username' => 'admin',
            'password' => 'admin',
            'roles' => ['ROLE_ADMIN'],
        ],
        [
            'username' => 'api',
            'password' => 'api',
            'roles' => ['ROLE_API'],
        ],
    ];

    public function load(ObjectManager $manager): void
    {

        foreach ($this->users as $user) {
            $newUser = (new User())
                ->setUsername($user['username'])
                ->setRoles($user['roles']);
            $plaintextPassword = $user['password'];

            $hashedPassword = $this->passwordHasher->hashPassword($newUser, $plaintextPassword);

            $newUser->setPassword($hashedPassword);

            $manager->persist($newUser);
        }

        $manager->flush();
    }
}
