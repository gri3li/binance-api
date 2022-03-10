<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\OrderType;
use Gri3li\TradingApiContracts\Price;
use Gri3li\TradingApiContracts\TimeInForce;

class Limit implements OrderType
{
	private Price $price;
	private TimeInForce $timeInForce;

	public function __construct(Price $price, TimeInForce $timeInForce)
	{
		$this->price = $price;
		$this->timeInForce = $timeInForce;
	}

	public function getParams(): array
	{
		return [
			'type' => 'LIMIT',
			'timeInForce' => $this->timeInForce->getParam(),
			'price' => $this->price->getParam(),
		];
	}
}
