<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

use Gri3li\TradingApiContracts\interfaces\SideInterface;

class SideResolver implements ValueResolverInterface
{
	private static array $map = [
		SideInterface::LONG => 'BUY',
		SideInterface::SHORT => 'SELL',
	];

	public function getParamFromValue(string $value): string
	{
		if (!isset(self::$map[$value])) {
			throw new UnresolvedValueException('Unresolved Param from Value');
		}

		return self::$map[$value];
	}

	public function getValueFromParam(string $param): string
	{
		foreach (self::$map as $v => $p) {
			if ($p === $param) {
				return $v;
			}
		}
		throw new UnresolvedValueException('Unresolved Value from Param');
	}
}
