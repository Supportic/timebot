<?php

declare(strict_types=1);

namespace App\Command\Personio;

use App\Service\Personio\Api\ApiAuthTokenServiceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractPersonioTokenRefreshCommand extends Command
{
    private const COMMAND_DESCRIPTION = 'Fetch and cache a new API auth token.';

    public function __construct(
        private readonly CacheItemPoolInterface $personioAuthCache,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription(self::COMMAND_DESCRIPTION);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $apiAuthTokenService = $this->getApiAuthTokenService();

        // Common token refresh logic
        $this->personioAuthCache->deleteItem($apiAuthTokenService->getCacheKey());
        $apiAuthTokenService->getAuthToken();

        $io->success('Auth token renewed');

        return Command::SUCCESS;
    }

    abstract protected function getApiAuthTokenService(): ApiAuthTokenServiceInterface;
}
