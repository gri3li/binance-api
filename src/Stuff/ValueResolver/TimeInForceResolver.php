<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

use Gri3li\TradingApiContracts\interfaces\TimeInForceInterface;

class TimeInForceResolver implements ValueResolverInterface
{
	private static array $allowed = [
		TimeInForceInterface::GOOD_TILL_CANCELED,
		TimeInForceInterface::IMMEDIATE_OR_CANCEL,
		TimeInForceInterface::FILL_OR_KILL,
		TimeInForceInterface::GOOD_TILL_DATE,
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
