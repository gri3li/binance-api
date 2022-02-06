<?php

namespace Gri3li\BinanceApi\Stuff\Repository;

use Gri3li\BinanceApi\Stuff\ValueObject\Auth;
use Gri3li\TradingApiContracts\interfaces\RecvWindowInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class DefaultRepository
{
	private Auth $auth;
	protected ClientInterface $client;
	private RecvWindowInterface $recvWindow;

	public function __construct(Auth $auth, ClientInterface $client, RecvWindowInterface $window)
	{
		$this->auth = $auth;
		$this->client = $client;
		$this->recvWindow = $window;
	}

	protected function defaultParams(): array
	{
		return [
			'recvWindow' => (int) (string) $this->recvWindow,
			'timestamp' => array_reduce(explode(' ', microtime()), static fn (int $carry, string $item) => $carry + round($item * 1000), 0),
		];
	}

	public function setRecvWindow(RecvWindowInterface $window): void
	{
		$this->recvWindow = $window;
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
		$signedRequest = Utils::modifyRequest($request, $changes);

		return $this->client->send($signedRequest);
	}
}
