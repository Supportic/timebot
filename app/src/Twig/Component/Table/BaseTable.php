<?php

declare(strict_types=1);

namespace App\Twig\Component\Table;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Metadata\UrlMapping;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

// Don't define a template for this actract LiveComponent

#[AsLiveComponent(
    // ust use this.prop in the twig template to access the properties.
    exposePublicProps: false
)]
abstract class BaseTable
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    private const DEFAULT_PAGE_QUERY_ALIAS = 'p';

    /**
     * @template T of Entity
     * @return ServiceEntityRepository<T>
     */
    protected abstract function getRepository(): ServiceEntityRepository;

    #[LiveProp]
    public ?int $maxEntries = null;

    #[LiveProp(writable: true)]
    #[Assert\Positive]
    public int $perPage = 10;

    //////////////////////////
    // Pagination
    //////////////////////////

    // setting the "url" param updates the queryParameter in the url
    // https://symfony.com/bundles/ux-live-component/current/index.html#controlling-the-query-parameter-name
    #[LiveProp(
        writable: true,
        url: new UrlMapping(as: self::DEFAULT_PAGE_QUERY_ALIAS),
        modifier: 'modifyCurrentPageProp'
    )]
    #[Assert\Positive]
    public int $currentPage = 1;

    private string $pageQueryAlias = self::DEFAULT_PAGE_QUERY_ALIAS;

    /**
     * Override the $pageQueryAlias: <twig:UserTable customPageQueryAlias="page" />
     * @var null|string
     */
    #[LiveProp]
    public ?string $customPageQueryAlias = null;

    #[LiveProp]
    // null means show all
    public ?int $maxPaginationLinks = 4;

    #[LiveProp]
    #[Assert\Type('bool')]
    public bool $showPagination = true;

    #[LiveProp]
    #[Assert\Type('bool')]
    public bool $canJumpToEnds = true;

    #[LiveProp]
    #[Assert\Type('bool')]
    // display "..." entries to indicate that there are more pages available...
    public bool $showEllipsis = true;

    // include params only when you want to validate them, simple setter not needed
    public function mount(
        ?int $maxEntries = null,
        int $perPage = 10,
        bool $showPagination = true,
        int $currentPage = 1,
        int $maxPaginationLinks = 4,
    ): void {
        // ensure perPage is positive
        $this->perPage = $perPage <= 0 ? 10 : $perPage;
        $this->maxEntries = $maxEntries !== null && $maxEntries <= 0 ? null : $maxEntries;

        //////////////////////////
        // Pagination
        //////////////////////////

        // hide pagination when max links are 0 or less
        $this->showPagination = $maxPaginationLinks <= 0 ? false : $showPagination;
        // 4 links are recommended to avoid visual jumps where the ellipsis gets added
        $this->maxPaginationLinks = ($this->showEllipsis && $maxPaginationLinks < 4) ? 4 : $maxPaginationLinks;

        // Initial clamp
        $this->currentPage = $this->getSafePage($currentPage);
    }

    public function modifyCurrentPageProp(LiveProp $liveProp): LiveProp
    {
        if ($this->customPageQueryAlias) {
            $liveProp = $liveProp->withUrl(new UrlMapping(as: $this->customPageQueryAlias));
            $this->pageQueryAlias = $this->customPageQueryAlias;
        }

        return $liveProp;
    }

    /**
     * Get the total count, respecting maxEntries limit
     */
    public function getTotalCount(): int
    {
        $total = $this->getRepository()->count([]);
        return ($this->maxEntries !== null) ? min($total, $this->maxEntries) : $total;
    }

    /**
     * Get the users for the current paginated page.
     * @return User[]
     */
    public function getPaginatedEntries(): array
    {
        $safePage = $this->getSafePage();
        $offset = ($safePage - 1) * $this->perPage;

        // Calculate limit: normally perPage, but capped if we'd exceed maxEntries
        $limit = $this->perPage;
        if ($this->maxEntries !== null) {
            $limit = min($this->perPage, max(0, $this->maxEntries - $offset));
        }

        return ($limit <= 0) ? [] : $this->getRepository()->findBy([], ['id' => 'ASC'], $limit, $offset);
    }

    /**
     * Ensure the page is at least 1, and no more than totalPages
     * If totalPages is 0, we still default to page 1 to show the 'empty' state correctly)
     */
    public function getSafePage(?int $page = null): int
    {
        $page ??= $this->currentPage;
        return min(max(1, $page), max(1, $this->getTotalPages()));
    }

    public function shouldShowPagination(): bool
    {
        return $this->showPagination && $this->getTotalPages() > 1;
    }

    public function getPaginationRange(): array
    {
        $totalPages = $this->getTotalPages();
        if (!$this->maxPaginationLinks || $totalPages <= $this->maxPaginationLinks) {
            return range(1, max(1, $totalPages));
        }

        $start = max(1, $this->getSafePage() - (int)floor($this->maxPaginationLinks / 2));
        $end = min($totalPages, $start + $this->maxPaginationLinks - 1);

        return range(max(1, $end - $this->maxPaginationLinks + 1), $end);
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->getTotalCount() / $this->perPage);
    }

    #[LiveAction]
    public function goToPage(#[LiveArg] int $page): void
    {
        // sleep(2); // artificially delay
        $this->currentPage = $this->getSafePage($page);
    }
}
