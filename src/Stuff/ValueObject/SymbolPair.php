<?php

namespace Gri3li\BinanceApi\Stuff\ValueObject;

use Gri3li\TradingApiContracts\Symbol;
use Gri3li\TradingApiContracts\SymbolPair as SymbolPairInterface;

class SymbolPair implements SymbolPairInterface
{
	private Symbol $base;
	private Symbol $quote;

	public function __construct(Symbol $base, Symbol $quote)
	{
		$this->base = $base;
		$this->quote = $quote;
	}

	public function getBase(): Symbol
	{
		return $this->base;
	}

	public function getQuote(): Symbol
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
