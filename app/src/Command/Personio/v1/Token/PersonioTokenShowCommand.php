<?php

declare(strict_types=1);

namespace App\Command\Personio\v1\Token;

use App\Service\Personio\Api\v1\ApiAuthTokenService;
use App\Command\Personio\AbstractPersonioTokenShowCommand;
use App\Helper\TimeHelper;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:personio:v1:token:show')]
class PersonioTokenShowCommand extends AbstractPersonioTokenShowCommand
{
    public function __construct(
        private readonly CacheItemPoolInterface $personioAuthCache,
        private readonly TimeHelper $timeHelper,
        private readonly ApiAuthTokenService $apiAuthTokenService,
    ) {
        parent::__construct($personioAuthCache, $timeHelper);
    }

    protected function getApiAuthTokenService(): ApiAuthTokenService
    {
        return $this->apiAuthTokenService;
    }
}
