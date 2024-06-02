<?php

namespace App\Model\Personio\Api;

class AuthToken
{
  public function __construct(
    private string $token,
    private int $expiresIn
  ) {
  }

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
}
