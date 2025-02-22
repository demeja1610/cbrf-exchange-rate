<?php

namespace Demeja1610\CBRFExchangeRate;

use DateTime;
use SoapFault;
use SoapClient;

/**
 * @throws SoapFault
 */
final class CBRClient
{
    private SoapClient $soapClient;
    private array $options = [
        'version' => 'SOAP_1_2'
    ];

    public function __construct(?array $options = null)
    {
        if (is_array($options)) {
            $this->options = $options;
        }

        $this->soapClient = new SoapClient(
            CentralBankRussian::WSDL,
            $this->options
        );
    }

    public function getExchangeRate(DateTime $date): mixed
    {
        $method = CentralBankRussian::METHOD_GET_EXCHANGE_RATE;

        return $this->soapClient->$method([
            'On_date' => $date->format('Y-m-d')
        ]);
    }

    /**
     * Перечень ежедневных валют
     */
    public function getCurrencyCodesDaily(): mixed
    {
        return $this->getCurrencyCodes(false);
    }

    /**
     * Перечень ежемесячных валют
     */
    public function getCurrencyCodesMonthly(): mixed
    {
        return $this->getCurrencyCodes(true);
    }

    private function getCurrencyCodes(bool $type): mixed
    {
        $method = CentralBankRussian::METHOD_GET_CURRENCY_CODES;

        return $this->soapClient->$method([
            'Seld' => $type
        ]);
    }
}
