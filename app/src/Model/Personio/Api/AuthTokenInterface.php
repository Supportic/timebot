<?php

declare(strict_types=1);

namespace App\Model\Personio\Api;

interface AuthTokenInterface
{
    public function getToken(): string;
    public function setToken(string $token): self;
    public function getExpiresIn(): int;
    public function setExpiresIn(int $expiresIn): self;
}
