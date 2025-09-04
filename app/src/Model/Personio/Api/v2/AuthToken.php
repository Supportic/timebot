<?php

declare(strict_types=1);

namespace App\Model\Personio\Api\v2;

use App\Model\Personio\Api\AbstractAuthToken;

class AuthToken extends AbstractAuthToken
{
    public function __construct(
        string $token,
        int $expiresIn,
        private string $tokenType,
        private string $scope,
    ) {
        parent::__construct($token, $expiresIn);
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
