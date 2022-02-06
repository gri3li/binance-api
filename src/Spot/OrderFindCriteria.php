<?php

namespace Gri3li\BinanceApi\Spot;

use DateTimeInterface;
use Gri3li\BinanceApi\Stuff\Identifier;
use Gri3li\TradingApiContracts\interfaces\FindCriteriaInterface;
use Gri3li\TradingApiContracts\interfaces\IdentifierInterface;
use Gri3li\TradingApiContracts\interfaces\SymbolPairInterface;

// критерии нужно мапить так же как валуеобжекты
class OrderFindCriteria implements FindCriteriaInterface
{
	private SymbolPairInterface $symbolPair;
	private ?IdentifierInterface $identifier = null;
	private ?DateTimeInterface $startTime = null;
	private ?DateTimeInterface $endTime = null;
	private int $limit = 500;

	public function __construct(SymbolPairInterface $symbolPair)
	{
		$this->symbolPair = $symbolPair;
	}

	public function setIdentifier(IdentifierInterface $identifier): self
	{
		$this->identifier = $identifier;

		return $this;
	}

	public function setStartTime(DateTimeInterface $startTime): self
	{
		$this->startTime = $startTime;

		return $this;
	}

	public function setEndTime(DateTimeInterface $endTime): self
	{
		$this->endTime = $endTime;

		return $this;
	}

	public function setLimit(int $limit): self
	{
		if ($limit >= 1000) {
			throw new \Exception(); //TODO improve exception structure
		}
		$this->limit = $limit;

		return $this;
	}

	public function makeParams(): array
	{
		$params = [
			'symbol' => $this->symbolPair->getParam(),
		];
		if ($this->identifier && $this->identifier->getId()) {
			$params['orderId'] = $this->identifier->getId();
		}
		if ($this->startTime) {
			$params['startTime'] = $this->startTime; // optional, LONG
		}
		if ($this->endTime) {
			$params['endTime'] = $this->endTime; // optional, LONG
		}
		if ($this->limit) {
			$params['limit'] = $this->limit; // optional, LONG
		}

		return $params;
	}
}
