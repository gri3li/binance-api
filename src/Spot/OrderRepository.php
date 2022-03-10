<?php

namespace Gri3li\BinanceApi\Spot;

use Gri3li\BinanceApi\Stuff\Repository\DefaultRepository;
use Gri3li\BinanceApi\Stuff\ValueObject;
use Gri3li\TradingApiContracts\OrderFindCriteria;
use Gri3li\TradingApiContracts\Identifier;
use Gri3li\TradingApiContracts\Order;
use Gri3li\TradingApiContracts\OrderRepository as OrderRepositoryInterface;
use Gri3li\TradingApiContracts\OrderType;
use Gri3li\TradingApiContracts\SymbolPair;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Utils;
use Psr\Http\Client\ClientExceptionInterface;

class OrderRepository extends DefaultRepository implements OrderRepositoryInterface
{
	/**
	 * Add new order
	 * @throws ClientExceptionInterface
	 */
	public function add(Order $order, OrderType $type): void
	{
		$params = array_merge($this->defaultParams(), $type->getParams(), [
			'symbol' => $order->getSymbolPair()->getParam(),
			'side' => $order->getSide()->getParam(),
			'quantity' => $order->getVolume()->getParam(),
		]);
		if ($order->getIdentifier()->getClientId()) {
			$params['newClientOrderId'] = $order->getIdentifier()->getClientId();
		}
		$headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
		$request = new Request('POST', '/api/v3/order', $headers, http_build_query($params));
		$this->signAndSend($request);
	}

	/**
	 * Get order by clientId or id, throw exception if not found
	 * @throws ClientExceptionInterface
	 */
	public function getByIdentifier(Identifier $identifier, SymbolPair $symbolPair): Order
	{
		$params = $this->defaultParams();
		if ($identifier->getClientId()) {
			$params['origClientOrderId'] = $identifier->getClientId();
		}
		if ($identifier->getId()) {
			$params['orderId'] = $identifier->getId();
		}
		$params['symbol'] = $symbolPair->getParam();
		$request = new Request('GET', '/api/v3/order?' . http_build_query($params));
		$response = $this->signAndSend($request);
		$item = Utils::jsonDecode($response->getBody()->getContents(), true);
		if (!$item) {
			throw new \RuntimeException('not found'); //TODO improve exception structure
		}

		return $this->makeOrderValueObject($item);
	}

	/**
	 * Cancel order
	 * @throws ClientExceptionInterface
	 */
	public function cancel(Order $order): void
	{
		$params = $this->defaultParams();
		if ($order->getIdentifier()->getClientId()) {
			$params['origClientOrderId'] =  $order->getIdentifier()->getClientId();
		}
		if ($order->getIdentifier()->getId()) {
			$params['orderId'] = $order->getIdentifier()->getId();
		}
		$params['symbol'] = $order->getSymbolPair()->getParam();
		$headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
		$request =  new Request('DELETE', '/api/v3/order', $headers, http_build_query($params));
		$this->signAndSend($request);
	}

	/**
	 * Find orders by criteria
	 * @throws ClientExceptionInterface
	 */
	public function findAll(OrderFindCriteria $criteria): array
	{
		$params = array_merge($this->defaultParams(), $criteria->makeParams());
		$request = new Request('GET', '/api/v3/allOrders?' . http_build_query($params));
		$response = $this->signAndSend($request);
		$items = Utils::jsonDecode($response->getBody()->getContents(), true);
		$result = [];
		foreach ($items as $item) {
			$result[] = $this->makeOrderValueObject($item);
		}

		return $result;
	}

	private function makeOrderValueObject(array $data): ValueObject\Order
	{
		return new ValueObject\Order(
			new ValueObject\Side($this->sideResolver, null, $data['side']),
			new ValueObject\SymbolPair(
				new ValueObject\Symbol($this->symbolBaseResolver, null, $data['symbol']),
				new ValueObject\Symbol($this->symbolQuoteResolver, null, $data['symbol'])
			),
			new ValueObject\Volume($this->volumeResolver, null, $data['origQty']),
			new ValueObject\OrderStatus($this->orderStatusResolver, null, $data['status']),
			new ValueObject\Identifier($data['clientOrderId'], $data['orderId'])
		);
	}
}
