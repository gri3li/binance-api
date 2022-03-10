<?php

namespace Gri3li\BinanceApi\Tests\Integration\Helper;

use Gri3li\BinanceApi\Spot\OrderRepository;
use Gri3li\BinanceApi\Stuff\ValueObject\Auth;
use Gri3li\BinanceApi\Stuff\ValueObject\RecvWindow;
use Gri3li\BinanceApi\Stuff\ValueResolver\RecvWindowResolver;
use GuzzleHttp\Client;

/**
 * tip: constants (API_KEY, API_SECRET, API_HOST) set in phpunit.xml
 */
class RepositoryHelper
{
	public static function getOrderRepository(): OrderRepository
	{
		return new OrderRepository(
			new Auth(API_KEY, API_SECRET),
			new Client(['base_uri' => API_HOST]),
			new RecvWindow(new RecvWindowResolver(), 5000)
		);
	}
}
