<?php

declare(strict_types=1);

namespace App\Twig\Component\Table;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * exposePublicProps=false must use this.prop in the twig template to access the properties.
 */
#[AsLiveComponent(
    exposePublicProps: false
)]
class UserTable
{
    use DefaultActionTrait;

    /**
     * null means no limit
     */
    #[LiveProp]
    public ?int $maxEntries = null;

    /**
     * null means no pagination
     * When $perPage is not marked as a #[LiveProp], it gets reset to its default value during live updates. When you click a pagination button, the component re-renders and $perPage reverts to 10, ignoring your pagination settings.
     */
    #[LiveProp]
    public ?int $perPage = 10;

    #[LiveProp(writable: true)]
    public int $currentPage = 1;

    /**
     * Maximum number of page links to display (null = show all)
     */
    #[LiveProp]
    public ?int $maxPaginationLinks = 4;

    /** @var User[] */
    protected array $users = [];

    public function __construct(
        private UserRepository $userRepository,
    ) {}

    /**
     * @param User[] $users
     */
    public function mount(
        array $users = [],
        int $currentPage = 1,
        ?int $maxEntries = null,
        ?int $perPage = 10,
    ): void {
        $this->users = $users;

        // Ensure perPage is positive
        $this->perPage = $perPage !== null && $perPage <= 0 ? 10 : $perPage;

        // Ensure maxEntries is positive
        $this->maxEntries = $maxEntries !== null && $maxEntries <= 0 ? null : $maxEntries;

        // Calculate total pages before setting currentPage
        $totalPages = $this->getTotalPages();

        // Ensure currentPage is at least 1 and not higher than total pages
        $this->currentPage = match (true) {
            $currentPage < 1 => 1,
            $currentPage > $totalPages => $totalPages,
            default => $currentPage,
        };
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        // Get all users (respecting maxEntries limit)
        if ([] === $this->users) {
            $allUsers = $this->userRepository->findBy([], limit: $this->maxEntries);
        } else {
            $allUsers = $this->maxEntries !== null && count($this->users) > $this->maxEntries
                ? array_slice($this->users, 0, $this->maxEntries)
                : $this->users;
        }

        // Apply pagination if enabled
        if (!$this->hasPagination()) {
            return $allUsers;
        }

        $offset = ($this->currentPage - 1) * $this->perPage;
        return array_slice($allUsers, $offset, $this->perPage);
    }

    public function hasPagination(): bool
    {
        return null !== $this->perPage;
    }

    public function shouldShowPagination(): bool
    {
        if (!$this->hasPagination()) {
            return false;
        }

        return $this->getTotalPages() > 1;
    }

    public function getPaginationRange(): array
    {
        $totalPages = $this->getTotalPages();

        if ($this->maxPaginationLinks === null || $totalPages <= $this->maxPaginationLinks) {
            return range(1, $totalPages);
        }

        $half = (int) floor($this->maxPaginationLinks / 2);
        $start = max(1, $this->currentPage - $half);
        $end = min($totalPages, $start + $this->maxPaginationLinks - 1);

        // Adjust start if we're near the end
        if ($end - $start + 1 < $this->maxPaginationLinks) {
            $start = max(1, $end - $this->maxPaginationLinks + 1);
        }

        return range($start, $end);
    }

    public function getTotalPages(): int
    {
        if (!$this->hasPagination()) {
            return 1;
        }

        $allUsers = [] === $this->users
            ? $this->userRepository->findBy([], limit: $this->maxEntries)
            : ($this->maxEntries !== null && count($this->users) > $this->maxEntries
                ? array_slice($this->users, 0, $this->maxEntries)
                : $this->users);

        return (int) ceil(count($allUsers) / $this->perPage);
    }

    #[LiveAction]
    public function goToPage(#[LiveArg] int $page): void
    {
        $totalPages = $this->getTotalPages();
        if ($page >= 1 && $page <= $totalPages) {
            $this->currentPage = $page;
        }
    }
}
