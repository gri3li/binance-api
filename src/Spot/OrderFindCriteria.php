<?php

namespace Gri3li\BinanceApi\Spot;

use DateTimeInterface;
use Gri3li\TradingApiContracts\SymbolPair;
use Gri3li\TradingApiContracts\OrderFindCriteria as OrderFindCriteriaInterface;

class OrderFindCriteria implements OrderFindCriteriaInterface
{
	private SymbolPair $symbolPair;
	private ?DateTimeInterface $startTime = null;
	private ?DateTimeInterface $endTime = null;
	private int $limit = 500;

	public function __construct(SymbolPair $symbolPair)
	{
		$this->symbolPair = $symbolPair;
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
		$this->limit = $limit;

		return $this;
	}

	public function makeParams(): array
	{
		$params = [
			'symbol' => $this->symbolPair->getParam(),
			'limit' => $this->limit,
		];
		if ($this->startTime) {
			$params['startTime'] = $this->startTime->format('Uv'); // milliseconds
		}
		if ($this->endTime) {
			$params['endTime'] = $this->endTime->format('Uv'); // milliseconds
		}

		return $params;
	}
}
