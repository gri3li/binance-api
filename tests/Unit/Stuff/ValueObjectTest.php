<?php

namespace Gri3li\Binance\Tests\Unit\Stuff;

use Gri3li\BinanceApi\Stuff\ValueObject\OrderType\Limit;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderType\LimitMaker;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderType\Market;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderType\StopLoss;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderType\TakeProfit;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderType\TakeProfitLimit;
use Gri3li\BinanceApi\Stuff\ValueObject\Order;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderStatus;
use Gri3li\BinanceApi\Stuff\ValueObject\Price;
use Gri3li\BinanceApi\Stuff\ValueObject\RecvWindow;
use Gri3li\BinanceApi\Stuff\ValueObject\Side;
use Gri3li\BinanceApi\Stuff\ValueObject\Suit;
use Gri3li\BinanceApi\Stuff\ValueObject\SymbolPair;
use Gri3li\BinanceApi\Stuff\ValueObject\Symbol;
use Gri3li\BinanceApi\Stuff\ValueObject\Identifier;
use Gri3li\BinanceApi\Stuff\ValueObject\TimeInForce;
use Gri3li\BinanceApi\Stuff\ValueObject\Volume;
use Gri3li\BinanceApi\Stuff\ValueResolver\OrderStatusResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\PriceResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\RecvWindowResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SideResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolBaseResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolQuoteResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\TimeInForceResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\UnresolvedValueException;
use Gri3li\BinanceApi\Stuff\ValueResolver\VolumeResolver;
use Gri3li\TradingApiContracts\interfaces\OrderStatusInterface;
use Gri3li\TradingApiContracts\interfaces\SideInterface;
use Gri3li\TradingApiContracts\interfaces\TimeInForceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use PHPUnit\Framework\TestCase;

class ValueObjectTest extends TestCase
{
	public function testIdentifier(): void
	{
		var_dump(Suit::Clubs->name);
		exit;



		$identifier = new Identifier('client-id-123');
		$this->assertEquals('client-id-123', $identifier->getClientId());
		$identifier = new Identifier(null, 'id-123');
		$this->assertEquals('id-123', $identifier->getId());
	}

	public function testOrderStatus(): void
	{
		$status = new OrderStatus(new OrderStatusResolver(), OrderStatus::NEW);
		$this->assertEquals(OrderStatus::NEW, $status);
		try {
			new OrderStatus(new OrderStatusResolver(), 'invalid-value');
			$this->fail('An unexpected exception was thrown');
		} catch (\Throwable $e) {
			$this->assertEquals(UnresolvedValueException::class, get_class($e));
		}
	}

	public function testPrice(): void
	{
		$price = new Price(new PriceResolver(), 111222333);
		$this->assertEquals('111222333', $price);
	}

	public function testRecvWindow(): void
	{
		$window = new RecvWindow(new RecvWindowResolver(), 5000);
		$this->assertEquals('5000', $window);
	}

	public function testSide(): void
	{
		$side = new Side(new SideResolver(), SideInterface::LONG);
		$this->assertEquals('BUY', $side->getParam());
	}

	public function testSymbol(): void
	{
		$symbol = new Symbol(new SymbolBaseResolver($this->clientForExchangeInfoRequest()), null,'ETHBTC');
		$this->assertEquals('ETHBTC', $symbol->getParam());
		$this->assertEquals('ETH', $symbol->getValue());
	}

	public function testSymbolPair(): void
	{
		$pair = new SymbolPair(
			new Symbol(new SymbolBaseResolver($this->clientForExchangeInfoRequest()), null,'ETHBTC'),
			new Symbol(new SymbolQuoteResolver($this->clientForExchangeInfoRequest()), null,'ETHBTC')
		);
		$this->assertEquals('ETH', $pair->getBase()->getValue());
		$this->assertEquals('BTC', $pair->getQuote()->getValue());
		$this->assertEquals('ETH/BTC', $pair->getValue());
	}

	public function testTimeInForce(): void
	{
		$tif = new TimeInForce(new TimeInForceResolver(), 'GTC');
		$this->assertEquals('GTC', $tif->getParam());
	}

	public function testVolume(): void
	{
		$volume = new Volume(new VolumeResolver(), '200');
		$this->assertEquals('200', $volume->getParam());
	}

	public function testOrder(): void
	{
		$order = new Order(
			new Side(new SideResolver(), SideInterface::LONG),
			new SymbolPair(
				new Symbol(new SymbolBaseResolver($this->clientForExchangeInfoRequest()), null,'ETHBTC'),
				new Symbol(new SymbolQuoteResolver($this->clientForExchangeInfoRequest()), null,'ETHBTC')
			),
			new Volume(new VolumeResolver(), '0.1'),
			new OrderStatus(new OrderStatusResolver(), OrderStatusInterface::NEW),
			new Identifier()
		);
		$this->assertEquals(SideInterface::LONG, $order->getSide()->getValue());
		$this->assertEquals('ETH/BTC', $order->getSymbolPair()->getValue());
		$this->assertEquals('0.1', $order->getVolume()->getValue());
		$this->assertEquals(OrderStatusInterface::NEW, $order->getStatus()->getValue());
	}

	public function testOrderType(): void
	{
		$price = new Price(new PriceResolver(), 100);
		$tif = new TimeInForce(new TimeInForceResolver(), TimeInForceInterface::GOOD_TILL_DATE);
		$types = [
			new Limit($price, $tif),
			new LimitMaker($price),
			new Market(),
			new StopLoss($price),
			new TakeProfit($price),
			new TakeProfitLimit($price, $price, $tif),
		];
		foreach ($types as $type) {
			$this->assertNotEmpty($type);
		}
	}

	public function clientForExchangeInfoRequest(): Client
	{
		$response = new Response(200, [], Utils::jsonEncode([
			'symbols' => [
				[
					'symbol' => 'ETHBTC',
					'status' => 'TRADING',
					'baseAsset' => 'ETH',
					'baseAssetPrecision' => 8,
					'quoteAsset' => 'BTC',
					'quotePrecision' => 8,
					'quoteAssetPrecision' => 8,
					'orderTypes' => [
						'LIMIT',
						'LIMIT_MAKER',
						'MARKET',
						'STOP_LOSS',
						'STOP_LOSS_LIMIT',
						'TAKE_PROFIT',
						'TAKE_PROFIT_LIMIT',
					],
					'icebergAllowed' => true,
					'ocoAllowed' => true,
					'isSpotTradingAllowed' => true,
					'isMarginTradingAllowed' => true,
					'permissions' => [
						'SPOT',
						'MARGIN',
					],
				],
			],
		]));

		return new Client(['handler' => HandlerStack::create(new MockHandler([$response]))]);
	}
}
