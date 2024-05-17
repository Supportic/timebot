<?php

declare(strict_types=1);

namespace App\Service\Personio\Api;

use AuthToken;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiAuthTokenService
{
  public const CACHE_KEY = 'personio.api.auth_token';

  public function __construct(
    private readonly HttpClientInterface $httpClient,
    private readonly CacheInterface $personioAuthCache,
    #[Autowire(param: 'app.personio.api.base_uri')]
    private readonly string $personioApiBaseUri,
    #[Autowire(env: 'APP_PERSONIO_CLIENT_ID')]
    private readonly string $personioClientId,
    #[Autowire(env: 'APP_PERSONIO_CLIENT_SECRET')]
    private readonly string $personioClientSecret,
  ) {
  }

  /**
   * Fetches a new auth token from the personio API. Old tokens stay valid up to 24h and cannot be invalided.
   * @throws HttpException
   * @throws AccessDeniedException
   */
  public function fetchAuthToken(): AuthToken
  {
    $authRoute = "{$this->personioApiBaseUri}/auth";

    $payload = [
      'client_id' => $this->personioClientId,
      'client_secret' => $this->personioClientSecret,
    ];

    $response = $this->httpClient->request(
      'POST',
      $authRoute,
      [
        'body' => json_encode($payload),
        'headers' => [
          'accept' => 'application/json',
          'content-type' => 'application/json',
        ],
      ]
    );

    $apiAuthResponse = json_decode($response->getContent(false));

    if (200 !== $response->getStatusCode() || !$apiAuthResponse->success) {
      throw new HttpException(
        $response->getStatusCode(),
        'Failed to fetch personio API auth token. ' . $apiAuthResponse->error->message
      );
    }

    return new AuthToken(
      $apiAuthResponse->data->token,
      $apiAuthResponse->data->expires_in
    );
  }

  /**
   * Retrieves the currently used auth token from the cache or from the API when expired.
   */
  public function getAuthToken(): string
  {
    // The callable will only be executed on a cache miss.
    $token = $this->personioAuthCache->get(
      self::CACHE_KEY,
      function (ItemInterface $item): string {

        $authToken = $this->fetchAuthToken();

        // lasts usually 24 hours
        $item->expiresAfter($authToken->getExpiresIn());

        return $authToken->getToken();
      }
    );

    return $token;
  }
}
