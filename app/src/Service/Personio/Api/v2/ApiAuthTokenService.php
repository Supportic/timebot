<?php

declare(strict_types=1);

namespace App\Service\Personio\Api\v2;

use App\Model\Personio\Api\v2\AuthToken;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiAuthTokenService
{
    public const CACHE_KEY = 'personio.api.v2.auth_token';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $personioAuthCache,
        #[Autowire(param: 'app.personio.api.v2.base_uri')]
        private readonly string $personioApiBaseUri,
        #[Autowire(env: 'APP_PERSONIO_CLIENT_ID')]
        private readonly string $personioClientId,
        #[Autowire(env: 'APP_PERSONIO_CLIENT_SECRET')]
        private readonly string $personioClientSecret,
    ) {}

    /**
     * Fetches a new auth token from the personio API. Old tokens stay valid up to 24h and cannot be invalided.
     * @throws HttpException
     * @throws AccessDeniedException
     */
    public function fetchAuthToken(): AuthToken
    {
        $authRoute = "{$this->personioApiBaseUri}/auth/token";

        $payload = [
            'client_id' => $this->personioClientId,
            'client_secret' => $this->personioClientSecret,
            'grant_type' => 'client_credentials',
        ];

        $response = $this->httpClient->request(
            'POST',
            $authRoute,
            [
                'body' => $payload,
                'headers' => [
                    'accept' => 'application/json',
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
            ]
        );

        $apiAuthResponse = json_decode($response->getContent(false));

        if (200 !== $response->getStatusCode()) {
            throw new HttpException(
                $response->getStatusCode(),
                'Failed to fetch personio API auth token. ' . $apiAuthResponse->error_description
            );
        }

        return new AuthToken(
            $apiAuthResponse->access_token,
            $apiAuthResponse->expires_in,
            $apiAuthResponse->token_type,
            $apiAuthResponse->scope,
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
