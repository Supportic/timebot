<?php

namespace App\Model\Personio\Api\v2;

class AuthToken
{
    public function __construct(
        private string $token,
        private int $expiresIn,
        private string $tokenType,
        private string $scope,
    ) {}

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(int $expiresIn): self
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function setTokenType(string $type): self
    {
        $this->tokenType = $type;

        return $this;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function setScope(string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }
}
