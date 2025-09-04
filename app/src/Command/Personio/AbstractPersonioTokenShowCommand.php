<?php

declare(strict_types=1);

namespace App\Command\Personio;

use App\Helper\TimeHelper;
use App\Service\Personio\Api\ApiAuthTokenServiceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Cache\ItemInterface;

abstract class AbstractPersonioTokenShowCommand extends Command
{
  private const COMMAND_DESCRIPTION = 'Display cached API auth token.';

  public function __construct(
    private readonly CacheItemPoolInterface $personioAuthCache,
    private readonly TimeHelper $timeHelper,
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

    /** @var ItemInterface */
    $tokenCacheItem = $this->personioAuthCache->getItem($apiAuthTokenService->getCacheKey());

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

  abstract protected function getApiAuthTokenService(): ApiAuthTokenServiceInterface;
}
