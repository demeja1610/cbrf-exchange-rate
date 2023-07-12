<?php

namespace Demeja1610\CBRFExchangeRate;

use SimpleXMLElement;
use Demeja1610\CBRFExchangeRate\Models\Currency;
use Demeja1610\CBRFExchangeRate\Collections\CurrencyCollection;
use Demeja1610\CBRFExchangeRate\Exceptions\ExceptionIncorrectData;

final class ReferenceData
{
    private CBRClient $CBRClient;

    public function __construct(CBRClient $CBRClient)
    {
        $this->CBRClient = $CBRClient;
    }

    /**
     * Перечень ежедневных валют
     * 
     * @throws ExceptionIncorrectData
     */
    public function getCurrencyCodesDaily(): CurrencyCollection
    {
        return $this->createCollection($this->CBRClient->getCurrencyCodesDaily());
    }

    /**
     * Перечень ежемесячных валют
     *
     * @throws ExceptionIncorrectData
     */
    public function getCurrencyCodesMonthly(): CurrencyCollection
    {
        return $this->createCollection($this->CBRClient->getCurrencyCodesMonthly());
    }

    /**
     * @throws ExceptionIncorrectData
     */
    private function createCollection(mixed $res): CurrencyCollection
    {
        $currenciesElement = new SimpleXMLElement($res->EnumValutesResult->any);

        if (!isset($currenciesElement->ValuteData) || !isset($currenciesElement->ValuteData->EnumValutes)) {
            throw new ExceptionIncorrectData('There is no correct response to the request.');
        }

        $list = $currenciesElement->ValuteData->EnumValutes;

        if (empty($list)) {
            throw new ExceptionIncorrectData('Invalid data in the response. No list of currencies');
        }

        $currencyCollection = new CurrencyCollection;

        foreach ($list as $item) {
            /**
             * таблица содержит поля:
             * Vcode — Внутренний код валюты
             * Vname — Название валюты
             * VEngname — Англ. название валюты
             * Vnom — Номинал
             * VcommonCode — Внутренний код валюты, являющейся ’базовой’**
             * VnumCode — цифровой код ISO
             * VcharCode — 3х буквенный код ISO
             */

            if (empty($item)) {
                continue;
            }

            $symbolCode = (string) ($item->VcharCode ?? '');

            if ($symbolCode === '') {
                continue;
            }

            $currency = new Currency;

            $currency
                ->setInternalCode($item->Vcode ?? '')
                ->setName($item->Vname ?? '')
                ->setNameEng($item->VEngname ?? '')
                ->setQuantity((int) ($item->Vnom ?? 1))
                ->setInternalCommonCode($item->VcommonCode ?? '')
                ->setNumericCode($item->VnumCode ?? '')
                ->setSymbolCode($symbolCode);

            $currencyCollection->add($currency);
        }

        return $currencyCollection;
    }
}
