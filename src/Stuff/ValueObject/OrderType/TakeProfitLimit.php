<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject\OrderType;

use Gri3li\TradingApiContracts\interfaces\OrderTypeInterface;
use Gri3li\TradingApiContracts\interfaces\PriceInterface;
use Gri3li\TradingApiContracts\interfaces\TimeInForceInterface;

class TakeProfitLimit implements OrderTypeInterface
{
	private PriceInterface $price;
	private PriceInterface $stopPrice;
	private TimeInForceInterface $timeInForce;

	public function __construct(PriceInterface $price, PriceInterface $stopPrice, TimeInForceInterface $timeInForce)
	{
		$this->price = $price;
		$this->stopPrice = $stopPrice;
		$this->timeInForce = $timeInForce;
	}

	public function getParams(): array
	{
		return [
			'type' => 'TAKE_PROFIT_LIMIT', // required, ENUM(LIMIT, MARKET, STOP_LOSS, STOP_LOSS_LIMIT, TAKE_PROFIT, TAKE_PROFIT_LIMIT, LIMIT_MAKER)
			'timeInForce' => (string) $this->timeInForce, // optional, ENUM(GTC, IOC, FOK)
			'price' => $this->price, // required, DECIMAL
			'stopPrice' => $this->stopPrice, // required, DECIMAL
		];
	}
}
