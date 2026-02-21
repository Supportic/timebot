<?php

declare(strict_types=1);

namespace App\Twig\Component\Table;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
    private const DEFAULT_SEARCH_QUERY_ALIAS = 'q';

    /**
     * @return ServiceEntityRepository<T>
     */
    protected abstract function getRepository(): ServiceEntityRepository;


    /**
     * Adding null will display all available entries. (warning: performance)
     * @var array<int, int|null>
     */
    #[LiveProp]
    public array $maxEntriesSelectionChoices = [10, 25, 50, 75, 100];

    /**
     * Define searchable fields for this table.
     * Return an array of field names that should be searched when a query is provided.
     * Example: ['username', 'email', 'firstName', 'lastName']
     *
     * @return string[]
     */
    protected abstract function getSearchableFields(): array;

    #[LiveProp(writable: true)]
    public ?int $maxEntries = 10;

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

    #[LiveProp]
    #[Assert\Type('bool')]
    public bool $paginationSelectEnabled = false;

    // ##############################
    // Search
    // ##############################

    #[LiveProp]
    #[Assert\Type('bool')]
    public bool $searchEnabled = false;

    #[LiveProp(writable: true, url: new UrlMapping(as: self::DEFAULT_SEARCH_QUERY_ALIAS))]
    public string $query = '';

    private string $searchQueryAlias = self::DEFAULT_SEARCH_QUERY_ALIAS;

    // include params only when you want to validate them, simple setter not needed
    public function mount(
        bool $paginationEnabled = true,
        int $paginationMaxNavItems = 4,
        int $paginationPage = 1,
        bool $paginationCanJumpToEnds = true,
        bool $paginationShowEllipsis = true,
        bool $searchEnabled = false,
        bool $paginationSelectEnabled = false,
    ): void {
        // 'strlen' is a common shorthand to filter out null
        $maxEntries = min(array_filter($this->maxEntriesSelectionChoices, 'strlen'));

        $this->maxEntries = $maxEntries !== null && $maxEntries <= 0 ? $this->maxEntries : $maxEntries;

        // hide pagination when max links are 0 or less
        $this->paginationEnabled = $paginationMaxNavItems <= 0 ? false : $paginationEnabled;

        $this->paginationCanJumpToEnds = $paginationCanJumpToEnds;
        $this->paginationShowEllipsis = $paginationShowEllipsis;

        // 4 links are recommended to avoid visual jumps where the ellipsis gets added
        $this->paginationMaxNavItems = ($paginationShowEllipsis && $paginationMaxNavItems < 4) ? 4 : $paginationMaxNavItems;

        $this->searchEnabled = $searchEnabled;
        $this->paginationSelectEnabled = $paginationSelectEnabled;

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

        if ($this->searchEnabled && !empty($this->query)) {
            return $this->getSearchResults($this->maxEntries);
        }

        return $this->getRepository()->findBy([], ['id' => 'ASC'], $this->maxEntries);
    }

    /**
     * Returns the total count from the database (or search results if searching), without any limit applied
     * Used for pagination calculations.
     */
    public function getTotalCount(): int
    {
        return $this->searchEnabled && !empty($this->query)
            ? $this->getSearchResultCount()
            : $this->getRepository()->count([]);
    }

    /**
     * Get the total count capped by the maxEntries limit
     * When maxEntries is null, shows all entries.
     * Used for display purposes-
     */
    public function getLimitedCount(): int
    {
        if ($this->searchEnabled && !empty($this->query)) {
            $total = $this->getSearchResultCount();
        } else {
            $total = $this->getRepository()->count([]);
        }

        // If maxEntries is null, show all entries
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
        $offset = ($safePage - 1) * $this->maxEntries;
        $limit = $this->maxEntries;

        if ($this->searchEnabled && !empty($this->query)) {
            return $this->getSearchResults($limit, $offset);
        }

        return $this->getRepository()->findBy([], ['id' => 'ASC'], $limit, $offset);
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
        // When maxEntries is null, show all on one page (no pagination)
        if ($this->maxEntries === null) {
            return 1;
        }

        return (int) ceil($this->getTotalCount() / $this->maxEntries);
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


    /**
     * Returns the offset start number of the visible pagination entries
     * e.g. <10>-15 of 15
     */
    public function getPaginationOffsetStart(): int
    {
        return ($this->getPaginationSafePage() - 1) * $this->maxEntries + 1;
    }

    /**
     * Returns the offset end number of the visible pagination entries
     * e.g. 10-<15> of 15
     */
    public function getPaginationOffsetEnd(): int
    {
        return min($this->getPaginationSafePage() * $this->maxEntries, $this->getTotalCount());
    }

    #[LiveAction]
    public function loadPaginationPage(#[LiveArg] int $page): void
    {
        // sleep(2); // artificially delay
        $this->paginationPage = $this->getPaginationSafePage($page);
    }

    // ##############################
    // Search
    // ##############################

    #[LiveAction]
    public function clearSearch(): void
    {
        $this->query = '';
        $this->paginationPage = 1;
    }

    /**
     * Build a QueryBuilder for searching entities based on the current query.
     * Searches across all fields defined in getSearchableFields().
     */
    protected function buildSearchQueryBuilder(): QueryBuilder
    {
        $qb = $this->getRepository()->createQueryBuilder('entity');
        $searchableFields = $this->getSearchableFields();

        if (empty($searchableFields) || empty($this->query)) {
            return $qb;
        }

        $orExpressions = [];
        $searchQuery = '%' . $this->query . '%';

        foreach ($searchableFields as $index => $field) {
            $orExpressions[] = $qb->expr()->like("entity.{$field}", ":search_{$index}");
        }

        if (!empty($orExpressions)) {
            $qb->where($qb->expr()->orX(...$orExpressions));

            foreach ($searchableFields as $index => $field) {
                $qb->setParameter("search_{$index}", $searchQuery);
            }
        }

        return $qb;
    }

    /**
     * Get search results with optional limit and offset for pagination.
     * @return array<int, T>
     */
    protected function getSearchResults(?int $limit = null, ?int $offset = null): array
    {
        $qb = $this->buildSearchQueryBuilder();

        $qb->orderBy('entity.id', 'ASC');

        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get the total count of search results.
     */
    protected function getSearchResultCount(): int
    {
        $qb = $this->buildSearchQueryBuilder();

        // Use COUNT for efficiency
        $qb->select('COUNT(entity.id)');

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
