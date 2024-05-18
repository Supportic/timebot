<?php

declare(strict_types=1);

namespace App\Personio\Api\HttpClient;

use App\Service\Personio\Api\ApiAuthTokenService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientFactory
{
  public function __construct(
    private readonly HttpClientInterface $httpClient,
    private readonly ApiAuthTokenService $apiAuthTokenService,
    #[Autowire(param: 'app.personio.api.base_uri')]
    private readonly string $personioApiBaseUri,
  ) {
  }

  public function create(): HttpClientInterface
  {
    $token = $this->apiAuthTokenService->getAuthToken();

    return $this->httpClient->withOptions([
      'base_uri' => $this->personioApiBaseUri,
      'auth_bearer' => $token,
      'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
      ],
    ]);
  }
}
