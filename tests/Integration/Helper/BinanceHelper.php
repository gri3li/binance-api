<?php

namespace Gri3li\BinanceApi\Tests\Integration\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Utils as GuzzleUtils;

/**
 * tip: constants (API_KEY, API_SECRET, API_HOST) set in phpunit.xml
 */
class BinanceHelper
{
	public static function get(string $uri, array $params): array
	{
		$params['timestamp'] = (new \DateTime())->format('Uv');
		$query = http_build_query($params);
		$signature = hash_hmac('sha256', $query, API_SECRET);
		$uri .= '?' . $query . '&signature=' . $signature;
		$headers = ['X-MBX-APIKEY' => API_KEY];
		$request = new Request('GET', $uri, $headers);
		$client = new Client(['base_uri' => API_HOST]);
		$response = $client->send($request);

		return (array) GuzzleUtils::jsonDecode($response->getBody()->getContents(), true);
	}

	public static function send(string $method, string $uri, array $params): array
	{
		$params['timestamp'] = (new \DateTime())->format('Uv');
		$query = http_build_query($params);
		$signature = hash_hmac('sha256', $query, API_SECRET);
		$body = $query . '&signature=' . $signature;
		$headers = [
			'X-MBX-APIKEY' => API_KEY,
			'Content-Type' => 'application/x-www-form-urlencoded',
			'Content-Length' => strlen($body),
		];
		$request = new Request($method, $uri, $headers, $body);
		$client = new Client(['base_uri' => API_HOST]);
		$response = $client->send($request);

		return (array) GuzzleUtils::jsonDecode($response->getBody()->getContents(), true);
	}

	public static function post(string $uri, array $params): array
	{
		return self::send('POST', $uri, $params);
	}

	public static function delete(string $uri, array $params): array
	{
		return self::send('DELETE', $uri, $params);
	}
}
