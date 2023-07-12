<?php

namespace Demeja16\CBRFExchangeRate\Models;

final class Currency
{
    private string $name = '';
    private string $nameEng = '';
    private int $quantity = 1;
    private string $numericCode = '';
    private string $symbolCode = '';
    private string $internalCode = '';
    private string $internalCommonCode = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $this->cleaner($name);
        return $this;
    }

    public function getNameEng(): string
    {
        return $this->nameEng;
    }

    public function setNameEng(string $nameEng): self
    {
        $this->nameEng = $this->cleaner($nameEng);
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
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

    public function getInternalCode(): string
    {
        return $this->internalCode;
    }

    public function setInternalCode(string $internalCode): self
    {
        $this->internalCode = $this->cleaner($internalCode);
        return $this;
    }

    public function getInternalCommonCode(): string
    {
        return $this->internalCommonCode;
    }

    public function setInternalCommonCode(string $internalCommonCode): self
    {
        $this->internalCommonCode = $this->cleaner($internalCommonCode);
        return $this;
    }

    private function cleaner(string $str): string
    {
        return trim(preg_replace('/\s\s+/', ' ', $str));
    }
}
