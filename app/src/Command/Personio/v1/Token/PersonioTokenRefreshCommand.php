<?php

declare(strict_types=1);

namespace App\Command\Personio\v1\Token;

use App\Service\Personio\Api\v1\ApiAuthTokenService;
use App\Command\Personio\AbstractPersonioTokenRefreshCommand;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:personio:v1:token:refresh')]
class PersonioTokenRefreshCommand extends AbstractPersonioTokenRefreshCommand
{
    public function __construct(
        private readonly CacheItemPoolInterface $personioAuthCache,
        private readonly ApiAuthTokenService $apiAuthTokenService,
    ) {
        parent::__construct($personioAuthCache);
    }

    protected function getApiAuthTokenService(): ApiAuthTokenService
    {
        return $this->apiAuthTokenService;
    }
}
