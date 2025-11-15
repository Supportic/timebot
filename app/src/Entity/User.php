<?php

namespace App\Entity;

use App\Enum\Role;
use App\Enum\UserState;
use App\Entity\Trait\SoftDeletableTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ValueError;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JsonSerializable
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Length(min: 5, max: 100)]
    #[Groups(['api'])]
    private ?string $username = null;

    /**
     * @var Role[] The user roles
     */
    #[ORM\Column(enumType: Role::class)]
    #[Groups(['api'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(enumType: UserState::class)]
    private ?UserState $state = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // don't set role USER to API users
        if (!in_array(Role::ROLE_API, $roles, true)) {
            // guarantee every user at least has ROLE_USER
            $roles[] = Role::ROLE_USER;
        }

        return array_unique(
            array_map(fn(Role $role) => $role->value, $roles),
        );
    }

    /**
     * @throws ValueError
     */
    public function getRole(): Role
    {
        $role = $this->roles[0] ?? Role::ROLE_USER;

        // defined(Role::class .'::' . $roleName)
        if (!Role::has($role)) {
            throw new \ValueError(sprintf('Undefined role "%s".', $role));
        }

        return $role;
    }

    /**
     * @param Role[] $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getState(): ?UserState
    {
        return $this->state;
    }

    public function setState(UserState $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Make private properties available when serializing e.g. to JSON.
     *
     * @return object{
     *  username: string,
     *  roles: string[],
     *  created_at: DatetimeImmutable,
     *  updated_at: DatetimeImmutable,
     *  deleted_at: DatetimeImmutable
     * }
     */
    public function jsonSerialize(): mixed
    {
        return [
            'username'      => $this->username,
            'roles'         => $this->roles,
            'state'         => $this->state->label(),
            'created_at'    => $this->createdAt->format(DATE_RFC3339_EXTENDED),
            'updated_at'    => $this->updatedAt->format(DATE_RFC3339_EXTENDED),
            'deleted_at'    => $this->updatedAt->format(DATE_RFC3339_EXTENDED),
        ];
    }
}
