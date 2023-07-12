<?php

namespace Demeja1610\CBRFExchangeRate;

use DateTime;
use Demeja1610\CBRFExchangeRate\Exceptions\ExceptionIncorrectData;
use Demeja1610\CBRFExchangeRate\Exceptions\ExceptionInvalidParameter;

final class Converter
{
    private DateTime $date;
    private ExchangeRate $exchangeRate;
    private int $precision = 2;

    public function __construct(ExchangeRate $exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
        $this->date = new DateTime();
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): Converter
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @throws ExceptionInvalidParameter
     */
    public function setPrecision(int $precision): self
    {
        if ($precision <= 0) {
            throw new ExceptionInvalidParameter('Precision must be greater than zero.');
        }

        $this->precision = $precision;

        return $this;
    }

    /**
     * @throws ExceptionIncorrectData
     * @throws ExceptionInvalidParameter
     */
    public function convert(float $val, string $symbolCodeFrom, string $symbolCodeTo): float
    {
        if ($symbolCodeFrom === '' || $symbolCodeTo === '') {
            throw new ExceptionInvalidParameter('The currency code is incorrect.');
        }

        $symbolCodeFrom = mb_strtoupper($symbolCodeFrom);
        $symbolCodeTo = mb_strtoupper($symbolCodeTo);

        if ($symbolCodeFrom === $symbolCodeTo) {
            return $val;
        }

        $currencyRateCollection = $this->exchangeRate
            ->setDate($this->date)
            ->getCurrencyExchangeRates();

        if ($currencyRateCollection === null) {
            throw new ExceptionIncorrectData('Invalid data received.');
        }

        $currencyRateFrom = $currencyRateCollection->getCurrencyRateBySymbolCode($symbolCodeFrom);

        if ($currencyRateFrom === null) {
            throw new ExceptionInvalidParameter(
                'Could not find data for the currency code: ' . $currencyRateFrom . '.'
            );
        }

        $currencyRateTo = $currencyRateCollection->getCurrencyRateBySymbolCode($symbolCodeTo);

        if ($currencyRateTo === null) {
            throw new ExceptionInvalidParameter(
                'Could not find data for the currency code: ' . $currencyRateTo . '.'
            );
        }

        $exchangeRateFrom = $currencyRateFrom->getExchangeRate() / $currencyRateFrom->getQuantity();

        $exchangeRateTo = $currencyRateTo->getExchangeRate() / $currencyRateTo->getQuantity();

        $res = ($exchangeRateFrom / $exchangeRateTo) * $val;

        return round($res, $this->precision);
    }
}
