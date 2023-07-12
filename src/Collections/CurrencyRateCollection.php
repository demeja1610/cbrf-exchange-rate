<?php

namespace Demeja1610\CBRFExchangeRate\Collections;

use Demeja1610\CBRFExchangeRate\Models\CurrencyRate;
use Iterator;

class CurrencyRateCollection implements Iterator
{
    private array $currencyRates = [];

    public function add(CurrencyRate $currencyRate): void
    {
        $this->currencyRates[$currencyRate->getSymbolCode()] = $currencyRate;
    }

    public function current(): CurrencyRate|false
    {
        return current($this->currencyRates);
    }

    public function next(): void
    {
        next($this->currencyRates);
    }

    public function key(): string
    {
        return (string) key($this->currencyRates);
    }

    public function valid(): bool
    {
        return key($this->currencyRates) !== null;
    }

    public function rewind(): void
    {
        reset($this->currencyRates);
    }

    public function count(): int
    {
        return count($this->currencyRates);
    }

    public function getCurrencyRateBySymbolCode(string $symbolCode): ?CurrencyRate
    {
        return $this->currencyRates[$symbolCode] ?? null;
    }
}
