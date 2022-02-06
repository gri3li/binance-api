<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject;

use Gri3li\TradingApiContracts\interfaces\IdentifierInterface;
use Gri3li\TradingApiContracts\interfaces\OrderInterface;
use Gri3li\TradingApiContracts\interfaces\OrderStatusInterface;
use Gri3li\TradingApiContracts\interfaces\SideInterface;
use Gri3li\TradingApiContracts\interfaces\SymbolPairInterface;
use Gri3li\TradingApiContracts\interfaces\VolumeInterface;

class Order implements OrderInterface
{
	private SideInterface $side;
	private SymbolPairInterface $symbolPair;
	private VolumeInterface $volume;
	private OrderStatusInterface $status;
	private IdentifierInterface $identifier;

	public function __construct(
		SideInterface $side,
		SymbolPairInterface $symbolPair,
		VolumeInterface $volume,
		OrderStatusInterface $status,
		IdentifierInterface $identifier
	)
	{
		$this->symbolPair = $symbolPair;
		$this->side = $side;
		$this->volume = $volume;
		$this->status = $status;
		$this->identifier = $identifier;
	}

	public function getIdentifier(): IdentifierInterface
	{
		return $this->identifier;
	}

	public function getStatus(): OrderStatusInterface
	{
		return $this->status;
	}

	public function getSymbolPair(): SymbolPairInterface
	{
		return $this->symbolPair;
	}

	public function getSide(): SideInterface
	{
		return $this->side;
	}

	public function getVolume(): VolumeInterface
	{
		return $this->volume;
	}
}
