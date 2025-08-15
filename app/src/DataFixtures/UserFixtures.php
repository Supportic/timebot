<?php

namespace App\DataFixtures;

use App\Entity\Enum\RolesEnum;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    public static function getGroups(): array
    {
        return ['test'];
    }

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
            'roles' => [RolesEnum::ROLE_SUPER_ADMIN->value],
        ],
        [
            'username' => 'admin',
            'password' => 'admin',
            'roles' => [RolesEnum::ROLE_ADMIN->value],
        ],
        [
            'username' => 'member',
            'password' => 'member',
            'roles' => [RolesEnum::ROLE_MEMBER->value],
        ],
        [
            'username' => 'api',
            'password' => 'api',
            'roles' => [RolesEnum::ROLE_API->value],
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
