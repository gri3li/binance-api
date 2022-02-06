<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject;

use Gri3li\TradingApiContracts\interfaces\SymbolInterface;
use Gri3li\TradingApiContracts\interfaces\SymbolPairInterface;

class SymbolPair implements SymbolPairInterface
{
	private SymbolInterface $base;
	private SymbolInterface $quote;

	public function __construct(SymbolInterface $base, SymbolInterface $quote)
	{
		$this->base = $base;
		$this->quote = $quote;
	}

	public function getBase(): SymbolInterface
	{
		return $this->base;
	}

	public function getQuote(): SymbolInterface
	{
		return $this->quote;
	}

	public function getParam(): string
	{
		return $this->base->getParam();
	}

	public function getValue(): string
	{
		return $this->base->getValue() . '/' . $this->quote->getValue();
	}

	public function __toString(): string
	{
		return $this->getValue();
	}
}
