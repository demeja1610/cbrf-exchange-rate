<?php

namespace Demeja1610\CBRFExchangeRate;

use Demeja1610\CBRFExchangeRate\Exceptions\ExceptionIncorrectData;
use Demeja1610\CBRFExchangeRate\Exceptions\ExceptionInvalidParameter;
use DateTime;
use Demeja1610\CBRFExchangeRate\Collections\CurrencyRateCollection;
use Demeja1610\CBRFExchangeRate\Models\CurrencyRate;
use SimpleXMLElement;

final class ExchangeRate
{
    private CBRClient $CBRClient;
    private DateTime $date;

    public function __construct(CBRClient $CBRClient)
    {
        $this->CBRClient = $CBRClient;
        $this->date = new DateTime();
    }

    /**
     * Возвращает коллекцию кусов валют
     *
     * @throws ExceptionIncorrectData|Exceptions\ExceptionInvalidParameter
     */
    public function getCurrencyExchangeRates(): CurrencyRateCollection
    {
        $res = $this->CBRClient->getExchangeRate($this->date);

        if (!is_object($res)) {
            throw new ExceptionIncorrectData('There is no correct response to the request.');
        }

        $rates = new SimpleXMLElement($res->GetCursOnDateResult->any);

        if (!isset($rates->ValuteData) || !isset($rates->ValuteData->ValuteCursOnDate)) {
            throw new ExceptionIncorrectData('Invalid data in the response.');
        }

        $list = $rates->ValuteData->ValuteCursOnDate;

        $currencyRateCollection = new CurrencyRateCollection();

        foreach ($list as $rate) {
            /**
             * Название свойств объекта $rate
             *
             * Vname — Название валюты
             * Vnom — Номинал
             * Vcurs — Курс
             * Vcode — ISO Цифровой код валюты
             * VchCode — ISO Символьный код валюты
             */

            if (!$this->checkCurrencyRate($rate)) {
                continue;
            }

            $symbolCode = (string) ($rate->VchCode ?? '');

            $currencyRate = new CurrencyRate();

            $currencyRate
                ->setName($rate->Vname ?? '')
                ->setExchangeRate((float) ($rate->Vcurs ?? 0))
                ->setQuantity((int) ($rate->Vnom ?? 1))
                ->setNumericCode($rate->Vcode ?? '')
                ->setSymbolCode($symbolCode);

            $currencyRateCollection->add($currencyRate);
        }

        $currencyRateCollection->add($this->rub());

        return $currencyRateCollection;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    private function checkCurrencyRate(SimpleXMLElement $rate): bool
    {
        return !empty($rate->VchCode)
            && !empty($rate->Vnom)
            && !empty($rate->Vcode)
            && !empty($rate->Vcurs);
    }

    /**
     * @throws ExceptionInvalidParameter
     */
    private function rub(): CurrencyRate
    {
        return (new CurrencyRate())
            ->setName(CentralBankRussian::NAME_RUB)
            ->setExchangeRate(1)
            ->setQuantity(1)
            ->setNumericCode(CentralBankRussian::NUMERIC_CODE_RUB)
            ->setSymbolCode(CentralBankRussian::SYMBOL_CODE_RUB);
    }

    /**
     * Возвращает курс обмена валюты в рублях.
     *
     * @throws ExceptionIncorrectData|ExceptionInvalidParameter
     */
    public function getRateInRubles(string $symbolCode): float
    {
        $symbolCode = strtoupper($symbolCode);

        $currencyRateCollection = $this->getCurrencyExchangeRates();

        $currencyRate = $currencyRateCollection->getCurrencyRateBySymbolCode($symbolCode);

        if ($currencyRate === null) {
            throw new ExceptionInvalidParameter('The currency code is incorrect.');
        }

        $value = $currencyRate->rateOneUnitInRubles();

        return (float) number_format($value, 4, '.', '');
    }
}
