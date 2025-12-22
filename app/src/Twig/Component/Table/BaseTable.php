<?php

declare(strict_types=1);

namespace App\Twig\Component\Table;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Metadata\UrlMapping;
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

    private const DEFAULT_PAGINATION_QUERY_ALIAS = 'p';

    /**
     * @return ServiceEntityRepository<T>
     */
    protected abstract function getRepository(): ServiceEntityRepository;

    #[LiveProp]
    public ?int $maxEntries = null;

    // ##############################
    // Pagination
    // ##############################

    #[LiveProp]
    #[Assert\Type('bool')]
    public bool $paginationEnabled = true;

    // setting the "url" param updates the queryParameter in the url
    // https://symfony.com/bundles/ux-live-component/current/index.html#controlling-the-query-parameter-name
    #[LiveProp(
        writable: true,
        url: new UrlMapping(as: self::DEFAULT_PAGINATION_QUERY_ALIAS),
        modifier: 'modifyPaginationPageProp',
    )]
    #[Assert\Positive]
    public int $paginationPage = 1;

    private string $paginationQueryAlias = self::DEFAULT_PAGINATION_QUERY_ALIAS;

    /**
     * Override the $paginationQueryAlias: <twig:Table:UserTable customPaginationQueryAlias="page" />
     * @var null|string
     */
    #[LiveProp]
    public ?string $paginationCustomQueryAlias = null;

    #[LiveProp(writable: true)]
    #[Assert\Positive]
    public int $paginationPerPageLimit = 10;

    #[LiveProp]
    // null means show all
    public ?int $paginationMaxNavItems = 4;

    #[LiveProp]
    #[Assert\Type('bool')]
    public bool $paginationCanJumpToEnds = true;

    #[LiveProp]
    #[Assert\Type('bool')]
    // display "..." entries to indicate that there are more pages available...
    public bool $paginationShowEllipsis = true;

    // include params only when you want to validate them, simple setter not needed
    public function mount(
        ?int $maxEntries = null,
        bool $paginationEnabled = true,
        int $paginationPerPageLimit = 10,
        int $paginationMaxNavItems = 4,
        int $paginationPage = 1,
        bool $paginationCanJumpToEnds = true,
        bool $paginationShowEllipsis = true,
    ): void {
        $this->maxEntries = $maxEntries !== null && $maxEntries <= 0 ? null : $maxEntries;
        $this->paginationPerPageLimit = $paginationPerPageLimit <= 0 ? 10 : $paginationPerPageLimit;

        // hide pagination when max links are 0 or less
        $this->paginationEnabled = $paginationMaxNavItems <= 0 ? false : $paginationEnabled;

        $this->paginationCanJumpToEnds = $paginationCanJumpToEnds;
        $this->paginationShowEllipsis = $paginationShowEllipsis;

        // 4 links are recommended to avoid visual jumps where the ellipsis gets added
        $this->paginationMaxNavItems = ($paginationShowEllipsis && $paginationMaxNavItems < 4) ? 4 : $paginationMaxNavItems;

        // Initial clamp - needs to be at the end
        $this->paginationPage = $this->getPaginationSafePage($paginationPage);
    }

    /**
     * Get the entries for the current paginated page.
     * @return array<int, T>
     */
    public function getEntries(): array
    {
        if ($this->paginationEnabled) {
            return $this->getPaginationEntries();
        }

        return $this->getRepository()->findBy([], ['id' => 'ASC'], $this->maxEntries);
    }

    /**
     * Get the total count, respecting maxEntries limit
     */
    public function getTotalEntryCount(): int
    {
        $total = $this->getRepository()->count([]);
        return ($this->maxEntries !== null) ? min($total, $this->maxEntries) : $total;
    }

    // ##############################
    // Pagination
    // ##############################

    public function modifyPaginationPageProp(LiveProp $liveProp): LiveProp
    {
        if ($this->paginationCustomQueryAlias) {
            $liveProp = $liveProp->withUrl(new UrlMapping(as: $this->paginationCustomQueryAlias));
            $this->paginationQueryAlias = $this->paginationCustomQueryAlias;
        }

        return $liveProp;
    }

    /**
     * Get the entries for the current paginated page.
     * @return array<int, T>
     */
    public function getPaginationEntries(): array
    {
        $safePage = $this->getPaginationSafePage();
        $offset = ($safePage - 1) * $this->paginationPerPageLimit;

        // Calculate limit: normally perPage, but capped if we'd exceed maxEntries
        $limit = $this->paginationPerPageLimit;
        if ($this->maxEntries !== null) {
            $limit = min($this->paginationPerPageLimit, max(0, $this->maxEntries - $offset));
        }

        return ($limit <= 0) ? [] : $this->getRepository()->findBy([], ['id' => 'ASC'], $limit, $offset);
    }

    public function showPagination(): bool
    {
        return $this->paginationEnabled && $this->getPaginationTotalPages() > 1;
    }

    /**
     * Ensure the page is at least 1, and no more than totalPages
     * If totalPages is 0, we still default to page 1 to show the 'empty' state correctly)
     */
    public function getPaginationSafePage(?int $page = null): int
    {
        $page ??= $this->paginationPage;
        return min(
            max(1, $page),
            max(1, $this->getPaginationTotalPages())
        );
    }

    public function getPaginationTotalPages(): int
    {
        return (int) ceil($this->getTotalEntryCount() / $this->paginationPerPageLimit);
    }

    public function getPaginationRange(): array
    {
        $totalPages = $this->getPaginationTotalPages();

        if (!$this->paginationMaxNavItems || $totalPages <= $this->paginationMaxNavItems) {
            return range(1, max(1, $totalPages));
        }

        $start = max(1, $this->getPaginationSafePage() - (int)floor($this->paginationMaxNavItems / 2));
        $end = min($totalPages, $start + $this->paginationMaxNavItems - 1);


        return range(max(1, $end - $this->paginationMaxNavItems + 1), $end);
    }

    #[LiveAction]
    public function loadPaginationPage(#[LiveArg] int $page): void
    {
        // sleep(2); // artificially delay
        $this->paginationPage = $this->getPaginationSafePage($page);
    }
}
