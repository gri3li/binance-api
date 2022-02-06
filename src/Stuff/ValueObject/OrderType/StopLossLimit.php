<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\interfaces\OrderTypeInterface;
use Gri3li\TradingApiContracts\interfaces\PriceInterface;

class StopLossLimit implements OrderTypeInterface
{
	private PriceInterface $stopPrice;

	public function __construct(PriceInterface $stopPrice)
	{
		$this->stopPrice = $stopPrice;
	}

	public function getParams(): array
	{
		return [
			'type' => 'STOP_LOSS', // required, ENUM(LIMIT, MARKET, STOP_LOSS, STOP_LOSS_LIMIT, TAKE_PROFIT, TAKE_PROFIT_LIMIT, LIMIT_MAKER)
			'stopPrice' => $this->stopPrice, // required, DECIMAL
		];
	}
}
