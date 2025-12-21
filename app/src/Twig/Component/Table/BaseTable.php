<?php

declare(strict_types=1);

namespace App\Twig\Component\Table;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

// Don't define a template for this abstract LiveComponent


/**
 * @template T of object
 */
#[AsLiveComponent(
    // use this.prop in the twig template to access the properties.
    exposePublicProps: false
)]
abstract class BaseTable
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    /**
     * @return ServiceEntityRepository<T>
     */
    protected abstract function getRepository(): ServiceEntityRepository;

    #[LiveProp]
    public ?int $maxEntries = null;

    #[LiveProp(writable: true)]
    #[Assert\Positive]
    public int $perPage = 10;

    #[LiveProp]
    #[Assert\PositiveOrZero]
    public int $currentPage = 1;

    // include params only when you want to validate them, simple setter not needed
    public function mount(
        ?int $maxEntries = null,
        int $perPage = 10,
        int $currentPage = 1,
    ): void {
        // ensure perPage is positive
        $this->perPage = $perPage <= 0 ? 10 : $perPage;
        $this->maxEntries = $maxEntries !== null && $maxEntries <= 0 ? null : $maxEntries;
        $this->currentPage = $currentPage;
    }

    /**
     * Get the total count, respecting maxEntries limit     *
     */
    public function getTotalCount(): int
    {
        $total = $this->getRepository()->count([]);
        return ($this->maxEntries !== null) ? min($total, $this->maxEntries) : $total;
    }

    /**
     * Get the entries for the current paginated page.
     * @return array<int, T>
     */
    public function getPaginatedEntries(): array
    {
        $offset = ($this->currentPage - 1) * $this->perPage;

        // Calculate limit: normally perPage, but capped if we'd exceed maxEntries
        $limit = $this->perPage;
        if ($this->maxEntries !== null) {
            $limit = min($this->perPage, max(0, $this->maxEntries - $offset));
        }

        /** @var array<int, T> $results */
        $results = ($limit <= 0)
            ? []
            : $this->getRepository()->findBy(
                [],
                ['id' => 'ASC'],
                $limit,
                $offset
            );

        return $results;
    }

    /**
     * Listen for pagination changes from the Pagination component
     */
    #[LiveListener('paginationPage:changed')]
    public function onPaginationPageChanged(#[LiveArg] int $page): void
    {
        $this->currentPage = $page;
    }
}
