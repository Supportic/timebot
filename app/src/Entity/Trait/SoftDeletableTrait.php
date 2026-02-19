<?php

namespace App\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/soft-deleteable.md
 * timeAware - can schedule deletions
 * hardDelete - allows deleting a second time to remove the entry completely
 */
#[Gedmo\SoftDeleteable(fieldName: 'deleted_at', timeAware: false, hardDelete: true)]
trait SoftDeletableTrait
{
    /**
     * Hook SoftDeleteable behavior
     * updates deletedAt field
     */
    // use SoftDeleteableEntity;

    #[ORM\Column('deleted_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['api'])]
    private ?\DateTimeImmutable $deletedAt = null;

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    /**
     * Using \DateTimeInterface allows you to pass either
     * Mutable or Immutable objects while maintaining internal immutability.
     */
    public function setDeletedAt(?\DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt instanceof \DateTime
            ? \DateTimeImmutable::createFromMutable($deletedAt)
            : $deletedAt;

        return $this;
    }
}
