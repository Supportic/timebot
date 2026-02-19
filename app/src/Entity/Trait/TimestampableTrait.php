<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\DBAL\Schema\DefaultExpression\CurrentTimestamp;
use Doctrine\DBAL\Types\Types;

trait TimestampableTrait
{
    // https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/timestampable.md#using-traits
    // use \Gedmo\Timestampable\Traits\TimestampableEntity;

    #[ORM\Column(
        'created_at',
        type: Types::DATETIME_IMMUTABLE,
        options: ['default' => new CurrentTimestamp()],
        // Prevent any future updates to this field
        updatable: false
    )]
    #[Groups(['api'])]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(
        'updated_at',
        type: Types::DATETIME_IMMUTABLE,
        options: ['default' => new CurrentTimestamp()],
    )]
    #[Groups(['api'])]
    #[Gedmo\Timestampable(on: 'update')]
    private \DateTimeImmutable $updatedAt;

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Using \DateTimeInterface allows you to pass either
     * Mutable or Immutable objects while maintaining internal immutability.
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt instanceof \DateTime
            ? \DateTimeImmutable::createFromMutable($createdAt)
            : $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Using \DateTimeInterface allows you to pass either
     * Mutable or Immutable objects while maintaining internal immutability.
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt instanceof \DateTime
            ? \DateTimeImmutable::createFromMutable($updatedAt)
            : $updatedAt;

        return $this;
    }
}
