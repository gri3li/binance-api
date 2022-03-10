<?php

namespace Gri3li\BinanceApi\Tests\Integration\Helper;

use Gri3li\BinanceApi\Stuff\ValueObject\Identifier;
use Gri3li\BinanceApi\Stuff\ValueObject\Order;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderStatus;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderType\Limit;
use Gri3li\BinanceApi\Stuff\ValueObject\Price;
use Gri3li\BinanceApi\Stuff\ValueObject\Side;
use Gri3li\BinanceApi\Stuff\ValueObject\Symbol;
use Gri3li\BinanceApi\Stuff\ValueObject\SymbolPair;
use Gri3li\BinanceApi\Stuff\ValueObject\TimeInForce;
use Gri3li\BinanceApi\Stuff\ValueObject\Volume;
use Gri3li\BinanceApi\Stuff\ValueResolver\OrderStatusResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\PriceResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SideResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolBaseResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolQuoteResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\TimeInForceResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\VolumeResolver;
use Gri3li\TradingApiContracts\OrderStatus as OrderStatusInterface;
use Gri3li\TradingApiContracts\TimeInForce as TimeInForceInterface;
use GuzzleHttp\Client;

class OrderHelper
{
	public static function getOrder(string $sideParam, string $symbolParam, string $volumeParam, string $clientId): Order
	{
		return new Order(
			new Side(new SideResolver(), null, $sideParam),
			self::getSymbolPair($symbolParam),
			new Volume(new VolumeResolver(), null, $volumeParam),
			new OrderStatus(new OrderStatusResolver(), OrderStatusInterface::NEW),
			new Identifier($clientId)
		);
	}

	public static function getOrderTypeLimit(string $priceParam): Limit
	{
		return new Limit(
			new Price(new PriceResolver(), null, $priceParam),
			new TimeInForce(new TimeInForceResolver(), TimeInForceInterface::GOOD_TILL_CANCELED)
		);
	}

	public static function getSymbolPair(string $symbolParam): SymbolPair
	{
		$client = new Client(['base_uri' => API_HOST]);
		return new SymbolPair(
			new Symbol(new SymbolBaseResolver($client), null,$symbolParam),
			new Symbol(new SymbolQuoteResolver($client), null,$symbolParam)
		);
	}
}
