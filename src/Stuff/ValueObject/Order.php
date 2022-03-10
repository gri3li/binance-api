<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject;

use Gri3li\TradingApiContracts\Identifier;
use Gri3li\TradingApiContracts\Order as OrderInterface;
use Gri3li\TradingApiContracts\OrderStatus;
use Gri3li\TradingApiContracts\Side;
use Gri3li\TradingApiContracts\SymbolPair;
use Gri3li\TradingApiContracts\Volume;

class Order implements OrderInterface
{
	private Side $side;
	private SymbolPair $symbolPair;
	private Volume $volume;
	private OrderStatus $status;
	private Identifier $identifier;

	public function __construct(
		Side $side,
		SymbolPair $symbolPair,
		Volume $volume,
		OrderStatus $status,
		Identifier $identifier
	)
	{
		$this->symbolPair = $symbolPair;
		$this->side = $side;
		$this->volume = $volume;
		$this->status = $status;
		$this->identifier = $identifier;
	}

	public function getIdentifier(): Identifier
	{
		return $this->identifier;
	}

	public function getStatus(): OrderStatus
	{
		return $this->status;
	}

	public function getSymbolPair(): SymbolPair
	{
		return $this->symbolPair;
	}

	public function getSide(): Side
	{
		return $this->side;
	}

	public function getVolume(): Volume
	{
		return $this->volume;
	}
}
