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
    name: 'app:personio:v2:token:invalidate',
    description: 'Delete cached API auth token.',
)]
class PersonioTokenInvalidateCommand extends Command
{
    public function __construct(
        private readonly CacheItemPoolInterface $personioAuthCache,
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tokenCacheItem = $this->personioAuthCache->getItem(ApiAuthTokenService::CACHE_KEY);

        if (is_null($tokenCacheItem->get())) {
            $io->info('Token not created yet');

            return Command::SUCCESS;
        }

        $this->personioAuthCache->deleteItem(ApiAuthTokenService::CACHE_KEY);

        // $command = new ArrayInput([
        //     'command' => 'cache:pool:delete',
        //     'pool' => 'personio.auth.cache',
        //     'key' => ApiAuthTokenService::CACHE_KEY
        // ]);

        // $returnCode = $this->getApplication()->doRun($command, $output);

        // if (0 != $returnCode) {
        //     $io->error('Failed to execute "cache:pool:delete"');
        //     return Command::FAILURE;
        // }

        $io->success('Auth token deleted');

        return Command::SUCCESS;
    }
}
