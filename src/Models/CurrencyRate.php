<?php

namespace Demeja1610\CBRFExchangeRate\Models;

use Demeja1610\CBRFExchangeRate\Exceptions\ExceptionInvalidParameter;

final class CurrencyRate
{
    private string $name = '';
    private float $exchangeRate = 1;
    private int $quantity = 1;
    private string $numericCode = '';
    private string $symbolCode = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $this->cleaner($name);
        return $this;
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }

    public function setExchangeRate(float $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        if ($quantity <= 0) {
            throw new ExceptionInvalidParameter('The quantity cannot be negative.');
        }

        $this->quantity = $quantity;
        return $this;
    }

    public function getNumericCode(): string
    {
        return $this->numericCode;
    }

    public function setNumericCode(string $numericCode): self
    {
        $this->numericCode = $this->cleaner($numericCode);
        return $this;
    }

    public function getSymbolCode(): string
    {
        return $this->symbolCode;
    }

    public function setSymbolCode(string $symbolCode): self
    {
        $this->symbolCode = $this->cleaner($symbolCode);
        return $this;
    }


    /**
     * Возвращает курс обмена валюты в рублях.
     */
    public function rateOneUnitInRubles(): float
    {
        $value = ($this->getExchangeRate() / $this->getQuantity());
        return (float) number_format($value, 4, '.', '');
    }

    private function cleaner(string $str): string
    {
        return trim(preg_replace('/\s\s+/', ' ', $str));
    }
}
