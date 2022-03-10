<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\OrderType;
use Gri3li\TradingApiContracts\Price;

class LimitMaker implements OrderType
{
	private Price $price;

	public function __construct(Price $price)
	{
		$this->price = $price;
	}

	public function getParams(): array
	{
		return [
			'type' => 'LIMIT_MAKER',
			'price' => $this->price->getParam(),
		];
	}
}
