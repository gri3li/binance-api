<?php

namespace Gri3li\Binance\Tests\Unit\Stuff;

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

class ValueResolverTest extends TestCase
{
	public function testOrderStatusResolver(): void
	{
		$resolver = new OrderStatusResolver();
		$value = OrderStatusInterface::NEW;
		$param = 'NEW';
		$this->assertEquals($param, $resolver->getParamFromValue($value));
		$this->assertEquals($value, $resolver->getValueFromParam($param));
		try {
			$thrown = false;
			$resolver->getParamFromValue('AZAZAOLOLO');
		} catch (UnresolvedValueException $e) {
			$thrown = true;
		} finally {
			$this->assertTrue($thrown, 'Non-existent status value should throw UnresolvedValueException');
		}
	}

	public function testPriceResolver(): void
	{
		$resolver = new PriceResolver();
		$value = 100;
		$param = 100;
		$this->assertEquals($param, $resolver->getParamFromValue($value));
		$this->assertEquals($value, $resolver->getValueFromParam($param));
	}

	public function testRecvWindowResolver(): void
	{
		$resolver = new RecvWindowResolver();
		$value = 100;
		$param = 100;
		$this->assertEquals($param, $resolver->getParamFromValue($value));
		$this->assertEquals($value, $resolver->getValueFromParam($param));
	}

	public function testSideResolver(): void
	{
		$resolver = new SideResolver();
		$map = [
			SideInterface::LONG => 'BUY',
			SideInterface::SHORT => 'SELL',
		];
		foreach ($map as $value => $param) {
			$this->assertEquals($param, $resolver->getParamFromValue($value));
			$this->assertEquals($value, $resolver->getValueFromParam($param));
		}
	}

	/**
	 * @dataProvider clientForExchangeInfoRequest
	 */
	public function testSymbolBaseResolver(Client $client): void
	{
		$resolver = new SymbolBaseResolver($client);
		$value = 'ETH';
		$param = 'ETHBTC';
		$this->assertEquals($param, $resolver->getParamFromValue($value));
		$this->assertEquals($value, $resolver->getValueFromParam($param));
	}

	/**
	 * @dataProvider clientForExchangeInfoRequest
	 */
	public function testSymbolQuoteResolver(Client $client): void
	{
		$resolver = new SymbolQuoteResolver($client);
		$value = 'BTC';
		$param = 'ETHBTC';
		$this->assertEquals($param, $resolver->getParamFromValue($value));
		$this->assertEquals($value, $resolver->getValueFromParam($param));
	}

	public function testTimeInForceResolver(): void
	{
		$resolver = new TimeInForceResolver();
		$value = TimeInForceInterface::GOOD_TILL_DATE;
		$param = TimeInForceInterface::GOOD_TILL_DATE;
		$this->assertEquals($param, $resolver->getParamFromValue($value));
		$this->assertEquals($value, $resolver->getValueFromParam($param));
	}

	public function testVolumeResolver(): void
	{
		$resolver = new VolumeResolver();
		$value = 100;
		$param = 100;
		$this->assertEquals($param, $resolver->getParamFromValue($value));
		$this->assertEquals($value, $resolver->getValueFromParam($param));
	}

	public function clientForExchangeInfoRequest(): array
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

		return [
			[
				new Client(['handler' => HandlerStack::create(new MockHandler([$response]))])
			],
		];
	}
}
