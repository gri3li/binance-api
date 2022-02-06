<?php

namespace Gri3li\BinanceApi\Spot;

use Gri3li\BinanceApi\Stuff\Repository\DefaultRepository;
use Gri3li\BinanceApi\Stuff\ValueObject\Identifier;
use Gri3li\BinanceApi\Stuff\ValueObject\Order;
use Gri3li\BinanceApi\Stuff\ValueObject\OrderStatus;
use Gri3li\BinanceApi\Stuff\ValueObject\Side;
use Gri3li\BinanceApi\Stuff\ValueObject\Symbol;
use Gri3li\BinanceApi\Stuff\ValueObject\SymbolPair;
use Gri3li\BinanceApi\Stuff\ValueObject\Volume;
use Gri3li\BinanceApi\Stuff\ValueResolver\OrderStatusResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SideResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolBaseResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolQuoteResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\VolumeResolver;
use Gri3li\TradingApiContracts\interfaces\FindCriteriaInterface;
use Gri3li\TradingApiContracts\interfaces\IdentifierInterface;
use Gri3li\TradingApiContracts\interfaces\OrderInterface;
use Gri3li\TradingApiContracts\interfaces\OrderRepositoryInterface;
use Gri3li\TradingApiContracts\interfaces\OrderTypeInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Utils;

class OrderRepository extends DefaultRepository implements OrderRepositoryInterface
{
	/**
	 * Add in a new order
	 * @param OrderInterface $order
	 * @param OrderTypeInterface $type
	 * @throws \Psr\Http\Client\ClientExceptionInterface
	 */
	public function add(OrderInterface $order, OrderTypeInterface $type): void
	{
		$params = array_merge($this->defaultParams(), $type->getParams(), [
			'symbol' => $order->getSymbolPair()->getParam(),
			'side' => $order->getSide()->getParam(),
			'quantity' => $order->getVolume()->getParam(),
		]);
		if ($order->getIdentifier()->getClientId()) {
			$params['clientOrderId'] = $order->getIdentifier()->getClientId();
		}
		$request = new Request('POST', '/api/v3/order', [], http_build_query($params));
		$this->signAndSend($request);
	}

	/**
	 * Get order by clientId or id, throw exception if not found
	 * @param IdentifierInterface $identifier
	 * @return OrderInterface
	 * @throws \Psr\Http\Client\ClientExceptionInterface
	 */
	public function getByIdentifier(IdentifierInterface $identifier): OrderInterface
	{
		$params = $this->defaultParams();
		if ($identifier->getClientId()) {
			$params['clientOrderId'] = $identifier->getClientId();
		}
		if ($identifier->getId()) {
			$params['orderId'] = $identifier->getId();
		}
		$request = new Request('GET', '/api/v3/order?' . http_build_query($params));
		$response = $this->signAndSend($request);
		$item = Utils::jsonDecode($response->getBody()->getContents(), true);
		if (!$item) {
			throw new \RuntimeException('not found'); //TODO improve exception structure
		}

		return new Order(
			new Side(new SideResolver(), null, $item['side']),
			new SymbolPair(
				new Symbol(new SymbolBaseResolver($this->client), null, $item['symbol']),
				new Symbol(new SymbolQuoteResolver($this->client), null, $item['symbol'])
			),
			new Volume(new VolumeResolver(), null, $item['origQty']),
			new OrderStatus(new OrderStatusResolver(), null, $item['status']),
			new Identifier($item['clientOrderId'], $item['orderId'])
		);
	}

	/**
	 * Cancel an active order by clientId or id
	 * @param IdentifierInterface $identifier
	 * @throws \Psr\Http\Client\ClientExceptionInterface
	 */
	public function cancelByIdentifier(IdentifierInterface $identifier): void
	{
		$params = $this->defaultParams();
		if ($identifier->getClientId()) {
			$params['clientOrderId'] = $identifier->getClientId();
		}
		if ($identifier->getId()) {
			$params['orderId'] = $identifier->getId();
		}
		$request =  new Request('POST', '/api/v3/order', [], http_build_query($params));
		$this->signAndSend($request);
	}

	/**
	 * Find orders by criteria
	 * @param FindCriteriaInterface $criteria
	 * @return array
	 * @throws \Psr\Http\Client\ClientExceptionInterface
	 */
	public function findAll(FindCriteriaInterface $criteria): array
	{
		$params = array_merge($this->defaultParams(), $criteria->makeParams());
		$request = new Request('GET', '/api/v3/order?' . http_build_query($params));
		$response = $this->signAndSend($request);
		$items = Utils::jsonDecode($response->getBody()->getContents(), true);
		$result = [];
		foreach ($items as $item) {
			$result[] = new Order(
				new Side(new SideResolver(), null, $item['side']),
				new SymbolPair(
					new Symbol(new SymbolBaseResolver($this->client), null, $item['symbol']),
					new Symbol(new SymbolQuoteResolver($this->client), null, $item['symbol'])
				),
				new Volume(new VolumeResolver(), null, $item['origQty']),
				new OrderStatus(new OrderStatusResolver(), null, $item['status']),
				new Identifier($item['clientOrderId'], $item['orderId'])
			);
		}

		return $result;
	}

	/**
	 * Find order by criteria, return null if not found
	 * @param FindCriteriaInterface $criteria
	 * @return OrderInterface|null
	 * @throws \Psr\Http\Client\ClientExceptionInterface
	 */
	public function findOne(FindCriteriaInterface $criteria): ?OrderInterface
	{
		$params = array_merge($this->defaultParams(), $criteria->makeParams());
		$request = new Request('GET', '/api/v3/order?' . http_build_query($params));
		$response = $this->signAndSend($request);
		$item = Utils::jsonDecode($response->getBody()->getContents(), true);
		if (!$item) {
			return null;
		}

		return new Order(
			new Side(new SideResolver(), null, $item['side']),
			new SymbolPair(
				new Symbol(new SymbolBaseResolver($this->client), null, $item['symbol']),
				new Symbol(new SymbolQuoteResolver($this->client), null, $item['symbol'])
			),
			new Volume(new VolumeResolver(), null, $item['origQty']),
			new OrderStatus(new OrderStatusResolver(), null, $item['status']),
			new Identifier($item['clientOrderId'], $item['orderId'])
		);
	}
}
