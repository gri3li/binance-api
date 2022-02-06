<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\interfaces\OrderTypeInterface;
use Gri3li\TradingApiContracts\interfaces\PriceInterface;

class LimitMaker implements OrderTypeInterface
{
	private PriceInterface $price;

	public function __construct(PriceInterface $price)
	{
		$this->price = $price;
	}

	public function getParams(): array
	{
		return [
			'type' => 'LIMIT_MAKER', // required, ENUM(LIMIT, MARKET, STOP_LOSS, STOP_LOSS_LIMIT, TAKE_PROFIT, TAKE_PROFIT_LIMIT, LIMIT_MAKER)
			'price' => $this->price, // required, DECIMAL
		];
	}
}
