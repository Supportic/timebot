<?php

declare(strict_types=1);

namespace App\Twig\Component;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Metadata\UrlMapping;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent(
    exposePublicProps: false
)]
class Pagination
{
    use DefaultActionTrait;
    use ValidatableComponentTrait;
    use ComponentToolsTrait; // Required for $this->emit()

    private const DEFAULT_PAGE_QUERY_ALIAS = 'p';

    // setting the "url" param updates the queryParameter in the url
    // https://symfony.com/bundles/ux-live-component/current/index.html#controlling-the-query-parameter-name
    #[LiveProp(
        writable: true,
        url: new UrlMapping(as: self::DEFAULT_PAGE_QUERY_ALIAS),
        modifier: 'modifyCurrentPageProp',
    )]
    #[Assert\Positive]
    public int $currentPage = 1;

    private string $pageQueryAlias = self::DEFAULT_PAGE_QUERY_ALIAS;

    /**
     * Override the $pageQueryAlias: <twig:Pagination customPageQueryAlias="page" />
     * @var null|string
     */
    #[LiveProp]
    public ?string $customPageQueryAlias = null;

    #[LiveProp]
    #[Assert\PositiveOrZero]
    public int $totalCount = 0;

    #[LiveProp]
    #[Assert\Positive]
    public int $perPage = 10;

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

    public function mount(
        int $currentPage = 1,
        int $totalCount = 0,
        int $perPage = 10,
        bool $showPagination = true,
        int $maxPaginationLinks = 4,
    ): void {
        $this->totalCount = $totalCount > 0 ? $totalCount : 0;
        $this->perPage = $perPage <= 0 ? 10 : $perPage;

        // hide pagination when max links are 0 or less
        $this->showPagination = $maxPaginationLinks <= 0 ? false : $showPagination;
        // 4 links are recommended to avoid visual jumps where the ellipsis gets added
        $this->maxPaginationLinks = ($this->showEllipsis && $maxPaginationLinks < 4) ? 4 : $maxPaginationLinks;

        // Initial clamp - needs to be at the end
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
     * Ensure the page is at least 1, and no more than totalPages
     * If totalPages is 0, we still default to page 1 to show the 'empty' state correctly)
     */
    public function getSafePage(?int $page = null): int
    {
        $page ??= $this->currentPage;
        return min(
            max(1, $page),
            max(1, $this->getTotalPaginationPages())
        );
    }

    public function getTotalPaginationPages(): int
    {
        return (int) ceil($this->totalCount / $this->perPage);
    }

    public function getPaginationRange(): array
    {
        $totalPages = $this->getTotalPaginationPages();
        if (!$this->maxPaginationLinks || $totalPages <= $this->maxPaginationLinks) {
            return range(1, max(1, $totalPages));
        }

        $start = max(1, $this->getSafePage() - (int)floor($this->maxPaginationLinks / 2));
        $end = min($totalPages, $start + $this->maxPaginationLinks - 1);

        return range(max(1, $end - $this->maxPaginationLinks + 1), $end);
    }

    #[LiveAction]
    public function goToPage(#[LiveArg] int $page): void
    {
        // sleep(2); // artificially delay

        $safePage = $this->getSafePage($page);
        $this->currentPage = $safePage;
        // Emit event to notify parent table components of page has changed
        $this->emitUp('paginationPage:changed', ['page' => $safePage]);
    }
}
