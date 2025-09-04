<?php

declare(strict_types=1);

namespace App\Command\Personio;

use App\Service\Personio\Api\ApiAuthTokenServiceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractPersonioTokenInvalidateCommand extends Command
{
    private const COMMAND_DESCRIPTION = 'Delete cached API auth token.';

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

        $tokenCacheItem = $this->personioAuthCache->getItem($apiAuthTokenService->getCacheKey());

        if (is_null($tokenCacheItem->get())) {
            $io->info('Token not created yet');

            return Command::SUCCESS;
        }

        $this->personioAuthCache->deleteItem($apiAuthTokenService->getCacheKey());

        // $command = new ArrayInput([
        //     'command' => 'cache:pool:delete',
        //     'pool' => 'personio.auth.cache',
        //     'key' => $apiAuthTokenService->getCacheKey()
        // ]);

        // $returnCode = $this->getApplication()->doRun($command, $output);

        // if (0 != $returnCode) {
        //     $io->error('Failed to execute "cache:pool:delete"');
        //     return Command::FAILURE;
        // }

        $io->success('Auth token deleted');

        return Command::SUCCESS;
    }

    abstract protected function getApiAuthTokenService(): ApiAuthTokenServiceInterface;
}
