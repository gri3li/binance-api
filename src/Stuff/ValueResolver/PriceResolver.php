<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

class PriceResolver implements ValueResolverInterface
{
	public function getParamFromValue(string $value): string
	{
		return $value;
	}

	public function getValueFromParam(string $param): string
	{
		return $param;
	}
}
