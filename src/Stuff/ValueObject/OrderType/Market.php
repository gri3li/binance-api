<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\OrderType;

class Market implements OrderType
{
	public function getParams(): array
	{
		return [
			'type' => 'MARKET',
		];
	}
}
