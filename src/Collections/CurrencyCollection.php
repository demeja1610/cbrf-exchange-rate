<?php

namespace Demeja1610\CBRFExchangeRate\Collections;

use Iterator;
use Demeja1610\CBRFExchangeRate\Models\Currency;

class CurrencyCollection implements Iterator
{
    private array $currencies = [];
 
    public function add(Currency $currency): void
    {
        $this->currencies[$currency->getSymbolCode()] = $currency;
    }

    public function current(): Currency|false
    {
        return current($this->currencies);
    }

    public function next(): void
    {
        next($this->currencies);
    }

    public function key(): string
    {
        return (string) key($this->currencies);
    }

    public function valid(): bool
    {
        return key($this->currencies) !== null;
    }

    public function rewind(): void
    {
        reset($this->currencies);
    }

    public function count(): int
    {
        return count($this->currencies);
    }

    public function getCurrencyBySymbolCode(string $symbolCode): ?Currency
    {
        return $this->currencies[$symbolCode] ?? null;
    }
}
