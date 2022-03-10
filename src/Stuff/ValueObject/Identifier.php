<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject;

use Gri3li\TradingApiContracts\Identifier as IdentifierInterface;

class Identifier implements IdentifierInterface
{
	private ?string $clientId;
	private ?string $id;

	public function __construct(?string $clientId = null, ?string $id = null)
	{
		$this->clientId = $clientId;
		$this->id = $id;
	}

	public function getClientId(): ?string
	{
		return $this->clientId;
	}

	public function getId(): ?string
	{
		return $this->id;
	}
}
