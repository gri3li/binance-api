<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject;

class Auth
{
	private string $apiKey;
	private string $secret;

	public function __construct(string $apiKey, string $secret)
	{
		$this->apiKey = $apiKey;
		$this->secret = $secret;
	}

	public function getApiKey(): string
	{
		return $this->apiKey;
	}

	public function getSecret(): string
	{
		return $this->secret;
	}
}
