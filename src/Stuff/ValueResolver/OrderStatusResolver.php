<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

use Gri3li\TradingApiContracts\OrderStatus;

class OrderStatusResolver implements ValueResolverInterface
{
	private static array $allowed = [
		OrderStatus::NEW,
		OrderStatus::FILLED,
		OrderStatus::PARTIALLY_FILLED,
		OrderStatus::CANCELED,
		OrderStatus::PENDING_CANCEL,
		OrderStatus::REJECTED,
		OrderStatus::EXPIRED,
	];

	public function getParamFromValue(string $value): string
	{
		if (!in_array($value, self::$allowed, true)) {
			throw new UnresolvedValueException('Unresolved Param from Value');
		}

		return $value;
	}

	public function getValueFromParam(string $param): string
	{
		if (!in_array($param, self::$allowed, true)) {
			throw new UnresolvedValueException('Unresolved Value from Param');
		}

		return $param;
	}
}
