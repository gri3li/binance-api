<?php

namespace Gri3li\Binance\Tests\Integration\Spot;

use Gri3li\BinanceApi\Spot\OrderFindCriteria;
use Gri3li\BinanceApi\Spot\OrderRepository;
use Gri3li\BinanceApi\Stuff\ValueObject\Auth;
use Gri3li\BinanceApi\Stuff\ValueObject\Identifier;
use Gri3li\BinanceApi\Stuff\ValueObject\Order;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderStatus;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderType\Limit;
use Gri3li\BinanceApi\Stuff\ValueObject\Price;
use Gri3li\BinanceApi\Stuff\ValueObject\RecvWindow;
use Gri3li\BinanceApi\Stuff\ValueObject\Side;
use Gri3li\BinanceApi\Stuff\ValueObject\Symbol;
use Gri3li\BinanceApi\Stuff\ValueObject\SymbolPair;
use Gri3li\BinanceApi\Stuff\ValueObject\TimeInForce;
use Gri3li\BinanceApi\Stuff\ValueObject\Volume;
use Gri3li\BinanceApi\Stuff\ValueResolver\OrderStatusResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\PriceResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\RecvWindowResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\ResolvableObject;
use Gri3li\BinanceApi\Stuff\ValueResolver\SideResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolBaseResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolQuoteResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\TimeInForceResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\VolumeResolver;
use Gri3li\TradingApiContracts\interfaces\OrderStatusInterface;
use Gri3li\TradingApiContracts\interfaces\SideInterface;
use Gri3li\TradingApiContracts\interfaces\TimeInForceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Utils as GuzzleUtils;
use GuzzleHttp\Psr7\Utils as Psr7Utils;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * tip: constants (API_KEY, API_SECRET, API_HOST) set in phpunit.xml
 */
class OrderRepositoryTest extends TestCase
{
	private Auth $auth;
	private Client $client;
	private RecvWindow $recvWindow;
	private OrderRepository $repository;

	public function __construct($name = null, array $data = [], $dataName = '')
	{
		$this->auth = new Auth(API_KEY, API_SECRET);
		$this->client = new Client(['base_uri' => API_HOST]);
		$this->recvWindow = new RecvWindow(new RecvWindowResolver(), 5000);
		$this->repository = new OrderRepository(
			$this->auth,
			$this->client,
			$this->recvWindow
		);
		parent::__construct($name, $data, $dataName);
	}

	public function testAdd(): void
	{
		$newOrder = new Order(
			new Side(new SideResolver(), SideInterface::SHORT),
			new SymbolPair(
				new Symbol(new SymbolBaseResolver($this->client), null,'BTCUSDT'),
				new Symbol(new SymbolQuoteResolver($this->client), null,'BTCUSDT')
			),
			new Volume(new VolumeResolver(), '0.0001'),
			new OrderStatus(new OrderStatusResolver(), OrderStatusInterface::NEW),
			new Identifier('id123')
		);
		$type = new Limit(
			new Price(new PriceResolver(), '123000'),
			new TimeInForce(new TimeInForceResolver(), TimeInForceInterface::GOOD_TILL_DATE)
		);
		//$this->repository->add($newOrder, $type);

		$params = http_build_query([
			'symbol' => 'BTCUSDT',
			'side' => 'BUY',
			'quantity' => '0.0001',
			'clientOrderId' => 'id123',
		]);
		$response = $this->signAndSend(new Request('GET', '/api/v3/order?' . $params));
		$item = GuzzleUtils::jsonDecode($response->getBody()->getContents(), true);

		$this->assertNotEmpty($item);
		$this->assertEquals($item['side'], $newOrder->getSide()->getParam());
		$this->assertEquals($item['symbol'], $newOrder->getSymbolPair()->getParam());
		$this->assertEquals($item['origQty'], $newOrder->getVolume()->getParam());
		$this->assertEquals($item['status'], $newOrder->getStatus()->getParam());
		$this->assertEquals($item['clientOrderId'], $newOrder->getIdentifier()->getClientId());
	}

	public function testGetByIdentifier(): void
	{
		$identifier = new Identifier('id123');
		//$order = $this->repository->getByIdentifier($identifier);
		$this->assertEquals($identifier->getClientId(), $order->getIdentifier()->getClientId());
	}

	public function testFindAll(): void
	{
		$symbolPair = new SymbolPair(
			new Symbol(new SymbolBaseResolver($this->client), null,'BTCUSDT'),
			new Symbol(new SymbolQuoteResolver($this->client), null,'BTCUSDT')
		);
		$criteria = new OrderFindCriteria($symbolPair);
		$orders = $this->repository->findAll($criteria);

		$this->assertTrue(true);
	}

	public function testFindOne(): void
	{
		$symbolPair = new SymbolPair(
			new Symbol(new SymbolBaseResolver($this->client), null,'BTCUSDT'),
			new Symbol(new SymbolQuoteResolver($this->client), null,'BTCUSDT')
		);
		$criteria = new OrderFindCriteria($symbolPair);
		$order = $this->repository->findOne($criteria);

		$this->assertTrue(true);
	}

	public function testCancel(): void
	{
		$this->assertTrue(true);
	}

	protected function signAndSend(RequestInterface $request): ResponseInterface
	{
		$changes = [
			'set_headers' => ['X-MBX-APIKEY' => $this->auth->getApiKey()],
		];
		if ($request->getBody()->getContents()) {
			$changes['body'] = $request->getBody()->getContents() . '&signature='
				. hash_hmac('sha256', $request->getBody()->getContents(), $this->auth->getSecret());
		} else {
			$changes['query'] = $request->getUri()->getQuery() . '&signature='
				. hash_hmac('sha256', $request->getUri()->getQuery(), $this->auth->getSecret());
		}
		$signedRequest = Psr7Utils::modifyRequest($request, $changes);

		return $this->client->send($signedRequest);
	}
}
