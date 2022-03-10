<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

use Gri3li\TradingApiContracts\TimeInForce;

class TimeInForceResolver implements ValueResolverInterface
{
	private static array $allowed = [
		TimeInForce::GOOD_TILL_CANCELED,
		TimeInForce::IMMEDIATE_OR_CANCEL,
		TimeInForce::FILL_OR_KILL,
		TimeInForce::GOOD_TILL_DATE,
	];

	public function getParamFromValue(string $value): string
	{
		if (!in_array($value, self::$allowed, true)) {
			throw new UnresolvedValueException('SymbolQuote Param not resolved');
		}

		return $value;
	}

	public function getValueFromParam(string $param): string
	{
		if (!in_array($param, self::$allowed, true)) {
			throw new UnresolvedValueException('SymbolQuote Value not resolved');
		}

		return $param;
	}
}
