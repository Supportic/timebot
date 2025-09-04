<?php

declare(strict_types=1);

namespace App\Command\Personio\v2\Token;

use App\Service\Personio\Api\v2\ApiAuthTokenService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:personio:v2:token:refresh',
    description: 'Fetch and cache a new API auth token.',
)]
class PersonioTokenRefreshCommand extends Command
{
    public function __construct(
        private readonly CacheItemPoolInterface $personioAuthCache,
        private readonly ApiAuthTokenService $apiAuthTokenService,
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->personioAuthCache->deleteItem(ApiAuthTokenService::CACHE_KEY);
        $this->apiAuthTokenService->getAuthToken();

        $io->success('Auth token renewed');

        return Command::SUCCESS;
    }
}
