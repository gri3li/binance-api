<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\OrderType;
use Gri3li\TradingApiContracts\Price;

class StopLossLimit implements OrderType
{
	private Price $stopPrice;

	public function __construct(Price $stopPrice)
	{
		$this->stopPrice = $stopPrice;
	}

	public function getParams(): array
	{
		return [
			'type' => 'STOP_LOSS',
			'stopPrice' => $this->stopPrice->getParam(),
		];
	}
}
