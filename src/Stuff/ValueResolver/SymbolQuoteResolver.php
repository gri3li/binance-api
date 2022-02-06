<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Utils;

class SymbolQuoteResolver implements ValueResolverInterface
{
	private array $exchangeInfo;

	public function __construct(ClientInterface $client)
	{
		$request = new Request('GET', '/api/v3/exchangeInfo');
		$response = $client->send($request);
		$this->exchangeInfo = (array) Utils::jsonDecode($response->getBody()->getContents(), true);
	}

	public function getParamFromValue(string $value): string
	{
		foreach ($this->exchangeInfo['symbols'] as $item) {
			if ($item['quoteAsset'] === $value) {
				return $item['symbol'];
			}
		}
		throw new UnresolvedValueException('Unresolved Param from Value');
	}

	public function getValueFromParam(string $param): string
	{
		foreach ($this->exchangeInfo['symbols'] as $item) {
			if ($item['symbol'] === $param) {
				return $item['quoteAsset'];
			}
		}
		throw new UnresolvedValueException('Unresolved Value from Param');
	}
}
