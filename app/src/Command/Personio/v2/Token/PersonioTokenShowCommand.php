<?php

declare(strict_types=1);

namespace App\Command\Personio\v2\Token;

use App\Helper\TimeHelper;
use App\Service\Personio\Api\v2\ApiAuthTokenService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\ItemInterface;

#[AsCommand(
    name: 'app:personio:v2:token:show',
    description: 'Display cached API auth token.',
)]
class PersonioTokenShowCommand extends Command
{
    public function __construct(
        private readonly CacheItemPoolInterface $personioAuthCache,
        private readonly TimeHelper $timeHelper,
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var ItemInterface */
        $tokenCacheItem = $this->personioAuthCache->getItem(ApiAuthTokenService::CACHE_KEY);

        if (is_null($tokenCacheItem->get())) {
            $io->info('Token not created yet');

            return Command::SUCCESS;
        }

        $metadata = $tokenCacheItem->getMetadata();
        $remainingTime = $this->timeHelper->calculateRemainingTime($metadata[ItemInterface::METADATA_EXPIRY]);

        $io->section('Token Information');

        $io->definitionList(
            ['Cache Key' => $tokenCacheItem->getKey()],
            ['Token' => $tokenCacheItem->get()],
            ['Expires in' => $this->timeHelper->formatDateInterval($remainingTime)],
        );

        return Command::SUCCESS;
    }
}
