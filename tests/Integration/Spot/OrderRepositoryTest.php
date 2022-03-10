<?php

namespace Gri3li\BinanceApi\Tests\Integration\Spot;

use Gri3li\BinanceApi\Spot\OrderFindCriteria;
use Gri3li\BinanceApi\Spot\OrderRepository;
use Gri3li\BinanceApi\Stuff\ValueObject\Identifier;
use Gri3li\BinanceApi\Stuff\ValueObject\SymbolPair;
use Gri3li\BinanceApi\Tests\Integration\Helper\BinanceHelper;
use Gri3li\BinanceApi\Tests\Integration\Helper\OrderHelper;
use Gri3li\BinanceApi\Tests\Integration\Helper\RepositoryHelper;
use Gri3li\TradingApiContracts\Order;
use Gri3li\TradingApiContracts\OrderStatus;
use PHPUnit\Framework\TestCase;

/**
 * @url https://binance-docs.github.io/apidocs/spot/en/#spot-account-trade
 */
class OrderRepositoryTest extends TestCase
{
	private OrderRepository $repository;
	private SymbolPair $symbolPair;

	public function __construct($name = null, array $data = [], $dataName = '')
	{
		throw new \RuntimeException('Attention! Testing on a real trading account. Remove this line to continue working');
		$openOrders = BinanceHelper::get('/api/v3/openOrders', ['symbol' => 'BTCUSDT']);
		if ($openOrders) {
			throw new \RuntimeException('Close all BTCUSDT orders before run test');
		}
		$this->repository = RepositoryHelper::getOrderRepository();
		$this->symbolPair = OrderHelper::getSymbolPair('BTCUSDT');
		parent::__construct($name, $data, $dataName);
	}


	public static function tearDownAfterClass(): void
	{
		$openOrders = BinanceHelper::get('/api/v3/openOrders', ['symbol' => 'BTCUSDT']);
		if ($openOrders) {
			BinanceHelper::delete('/api/v3/openOrders', ['symbol' => 'BTCUSDT']);
		}
	}

	/**
	 * @covers OrderRepository::add
	 */
	public function testAdd(): string
	{
		$clientId = uniqid('order_', false);
		$newOrder = OrderHelper::getOrder('BUY', 'BTCUSDT', '0.0003', $clientId);
		$type = OrderHelper::getOrderTypeLimit('40000');
		$this->repository->add($newOrder, $type);

		$data = BinanceHelper::get('/api/v3/order', [
			'symbol' => 'BTCUSDT',
			'origClientOrderId' => $clientId,
		]);

		$this->assertEquals($newOrder->getSide()->getParam(), $data['side']);
		$this->assertEquals($newOrder->getSymbolPair()->getParam(), $data['symbol']);
		$this->assertEquals($newOrder->getStatus()->getParam(), $data['status']);
		$this->assertEquals($newOrder->getIdentifier()->getClientId(), $data['clientOrderId']);

		return $clientId;
	}

	/**
	 * @depends testAdd
	 * @covers OrderRepository::getByIdentifier
	 * @covers OrderRepository::findAll
	 */
	public function testFetching(string $clientId): Order
	{
		$criteria = new OrderFindCriteria($this->symbolPair);
		$orders = $this->repository->findAll($criteria);
		$newOrders = array_filter($orders, fn (Order $order) => $order->getStatus()->getValue() === OrderStatus::NEW);
		$this->assertCount(1, $newOrders);

		$criteria->setStartTime(new \DateTime('-2 day'));
		$criteria->setEndTime(new \DateTime('-1 day'));
		$orders = $this->repository->findAll($criteria);
		$newOrders = array_filter($orders, fn (Order $order) => $order->getStatus()->getValue() === OrderStatus::NEW);
		$this->assertCount(0, $newOrders);

		$criteria->setStartTime(new \DateTime('-1 hour'));
		$criteria->setEndTime(new \DateTime('+1 hour'));
		$orders = $this->repository->findAll($criteria);
		$newOrders = array_filter($orders, fn (Order $order) => $order->getStatus()->getValue() === OrderStatus::NEW);
		$this->assertCount(1, $newOrders);

		$identifier = new Identifier($clientId);
		$order = $this->repository->getByIdentifier($identifier, $this->symbolPair);
		$this->assertEquals($clientId, $order->getIdentifier()->getClientId());

		return $order;
	}

	/**
	 * @depends testFetching
	 * @covers OrderRepository::cancel
	 */
	public function testCancel(Order $order): void
	{
		$this->repository->cancel($order);
		$openOrders = BinanceHelper::get('/api/v3/openOrders', ['symbol' => 'BTCUSDT']);
		$this->assertEmpty($openOrders);
	}
}
