<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

use Gri3li\TradingApiContracts\interfaces\OrderStatusInterface;

class OrderStatusResolver implements ValueResolverInterface
{
	private static array $allowed = [
		OrderStatusInterface::NEW,
		OrderStatusInterface::FILLED,
		OrderStatusInterface::PARTIALLY_FILLED,
		OrderStatusInterface::CANCELED,
		OrderStatusInterface::PENDING_CANCEL,
		OrderStatusInterface::REJECTED,
		OrderStatusInterface::EXPIRED,
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
