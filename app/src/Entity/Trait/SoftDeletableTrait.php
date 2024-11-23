<?php

namespace App\Entity\Trait;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Attribute\Groups;

#[Gedmo\SoftDeleteable(fieldName: 'deleted_at', timeAware: false, hardDelete: true)]
trait SoftDeletableTrait
{
    /**
     * Hook SoftDeleteable behavior
     * updates deletedAt field
     */
    // use SoftDeleteableEntity;

    #[ORM\Column('deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['api'])]
    private ?\DateTime $deletedAt = null;

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
