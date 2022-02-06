<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

interface ValueResolverInterface
{
	public function getParamFromValue(string $value): string;
	public function getValueFromParam(string $param): string;
}