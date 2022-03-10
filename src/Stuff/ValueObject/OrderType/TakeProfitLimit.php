<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\OrderType;
use Gri3li\TradingApiContracts\Price;
use Gri3li\TradingApiContracts\TimeInForce;

class TakeProfitLimit implements OrderType
{
	private Price $price;
	private Price $stopPrice;
	private TimeInForce $timeInForce;

	public function __construct(Price $price, Price $stopPrice, TimeInForce $timeInForce)
	{
		$this->price = $price;
		$this->stopPrice = $stopPrice;
		$this->timeInForce = $timeInForce;
	}

	public function getParams(): array
	{
		return [
			'type' => 'TAKE_PROFIT_LIMIT',
			'timeInForce' => $this->timeInForce->getParam(),
			'price' => $this->price->getParam(),
			'stopPrice' => $this->stopPrice->getParam(),
		];
	}
}
