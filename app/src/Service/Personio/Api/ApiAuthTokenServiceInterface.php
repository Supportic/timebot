<?php

declare(strict_types=1);

namespace App\Service\Personio\Api;

use App\Model\Personio\Api\AuthTokenInterface;

interface ApiAuthTokenServiceInterface
{
    public function getCacheKey(): string;
    public function fetchAuthToken(): AuthTokenInterface;
    public function getAuthToken(): string;
}
