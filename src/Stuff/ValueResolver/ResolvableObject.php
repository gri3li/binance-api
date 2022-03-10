<?php

namespace Gri3li\BinanceApi\Stuff\ValueResolver;

use Gri3li\BinanceApi\Stuff\Exception\InvalidArgumentException;

abstract class ResolvableObject
{
	private string $param;
	private string $value;

	public function __construct(ValueResolverInterface $resolver, ?string $value = null, ?string $param = null)
	{
		if (is_null($value) && is_null($param)) {
			throw new UnresolvedValueException('Value or Param required');
		}
		$this->value = $value ?? $resolver->getValueFromParam($param);
		$this->param = $param ?? $resolver->getParamFromValue($value);
	}

	public function getParam(): string
	{
		return $this->param;
	}

	public function getValue(): string
	{
		return $this->value;
	}

	public function __toString(): string
	{
		return $this->value;
	}
}
