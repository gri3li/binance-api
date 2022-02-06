<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\interfaces\OrderTypeInterface;

class Market implements OrderTypeInterface
{
	public function getParams(): array
	{
		return [
			'type' => 'MARKET', // required, ENUM(LIMIT, MARKET, STOP_LOSS, STOP_LOSS_LIMIT, TAKE_PROFIT, TAKE_PROFIT_LIMIT, LIMIT_MAKER)
		];
	}
}
