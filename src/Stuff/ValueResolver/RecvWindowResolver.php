<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;


class RecvWindowResolver implements ValueResolverInterface
{
	public function getParamFromValue(string $value): string
	{
		if ((int)$value > 60000) {
			throw new UnresolvedValueException('Value cannot be greater than 60000');
		}
		return $value;
	}

	public function getValueFromParam(string $param): string
	{
		return $param;
	}
}
