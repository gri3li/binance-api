<?php

namespace Gri3li\BinanceApi\Stuff\Repository;

use Gri3li\BinanceApi\Stuff\ValueResolver\OrderStatusResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SideResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolBaseResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\SymbolQuoteResolver;
use Gri3li\BinanceApi\Stuff\ValueResolver\VolumeResolver;
use Gri3li\BinanceApi\Stuff\ValueObject\Auth;
use Gri3li\TradingApiContracts\RecvWindow;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientExceptionInterface;

abstract class DefaultRepository
{
	private Auth $auth;
	protected ClientInterface $client;
	private RecvWindow $recvWindow;

	protected OrderStatusResolver $orderStatusResolver;
	protected SideResolver $sideResolver;
	protected SymbolBaseResolver $symbolBaseResolver;
	protected SymbolQuoteResolver $symbolQuoteResolver;
	protected VolumeResolver $volumeResolver;

	public function __construct(Auth $auth, ClientInterface $client, RecvWindow $window)
	{
		$this->auth = $auth;
		$this->client = $client;
		$this->recvWindow = $window;

		$this->orderStatusResolver = new OrderStatusResolver();
		$this->sideResolver = new SideResolver();
		$this->symbolBaseResolver = new SymbolBaseResolver($client);
		$this->symbolQuoteResolver = new SymbolQuoteResolver($client);
		$this->volumeResolver = new VolumeResolver();
	}

	protected function defaultParams(): array
	{
		return [
			'recvWindow' => $this->recvWindow->getParam(),
			'timestamp' => (new \DateTime())->format('Uv'), // milliseconds
		];
	}

	/**
	 * @param RecvWindow $window
	 * @return void
	 */
	public function setRecvWindow(RecvWindow $window): void
	{
		$this->recvWindow = $window;
	}

	/**
	 * @throws ClientExceptionInterface
	 */
	protected function signAndSend(RequestInterface $request): ResponseInterface
	{
		$changes = ['set_headers' => ['X-MBX-APIKEY' => $this->auth->getApiKey()]];
		$params = $request->getBody()->getContents();
		if ($params) {
			$changes['body'] = $params . '&signature=' . hash_hmac('sha256', $params, $this->auth->getSecret());
			$changes['set_headers']['Content-Length'] = strlen($changes['body']);
		} else {
			$params = $request->getUri()->getQuery();
			$changes['query'] = $params . '&signature=' . hash_hmac('sha256', $params, $this->auth->getSecret());
		}
		$signedRequest = Utils::modifyRequest($request, $changes);

		return $this->client->send($signedRequest);
	}
}
