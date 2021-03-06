<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle\Provider;

use Submarine\NbrbExchangeRatesBundle\Client\ApiClient;
use Submarine\NbrbExchangeRatesBundle\CurrencyRateDate;
use Submarine\NbrbExchangeRatesBundle\Exception\UndefinedCurrencyException;
use Submarine\NbrbExchangeRatesBundle\ExchangeRate;

/**
 * Class ExchangeRatesProvider
 * @package Submarine\NbrbParserBundle
 */
class ExchangeRatesProvider implements ExchangeRatesProviderInterface
{


    /**
     * Выкидывать исключения?
     * @var bool
     */
    private $showExceptions = true;


    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * ExchangeRatesProvider constructor.
     * @param ApiClient $apiClient
     * @param bool $showExceptions
     */
    public function __construct(ApiClient $apiClient, $showExceptions)
    {
        $this->apiClient = $apiClient;
        $this->showExceptions = $showExceptions;
    }


    /**
     * Все курсы валют за указанную дату
     *
     * @param \DateTime $date Если null - текущая дата
     *
     * @return ExchangeRate[]
     */
    public function getAllRatesExchanges(\DateTime $date = null)
    {
        if ($date === null) {
            $date = new \DateTime();
        }

        try {
            $body = $this->apiClient->getXmlExchangesRates($date, false);
            $xml = simplexml_load_string($body);

            $result = [];

            if (count($xml->Currency)) {
                foreach ($xml->Currency as $item) {
                    $exRate = ExchangeRate::createFromXML($item, $date);
                    $result[$exRate->getCharCode()] = $exRate;
                }
            }

            return $result;

        } catch (\Exception $exc) {
            if ($this->showExceptions) {
                throw new $exc;
            }

            return [];
        }
    }


    /**
     * Курсы выбранных валют за указанную дату
     *
     * @param array $codes Коды валют в формате ISO (USD, UAH)
     * @param \DateTime $date Дата, по умолчанию текущая дата
     *
     * @throws UndefinedCurrencyException
     * @return ExchangeRate[]
     */
    public function getRatesExchanges(array $codes, \DateTime $date = null)
    {
        $rates = $this->getAllRatesExchanges($date);
        $result = [];
        foreach ($codes as $code) {
            if (array_key_exists($code, $rates)) {
                $result[$code] = $rates[$code];
            } elseif ($this->showExceptions) {
                throw new UndefinedCurrencyException(sprintf('Undefined currency code: %s', $code));
            }
        }

        return $result;
    }


    /**
     * Курс валюты за указанную дату
     *
     * @param string $code Код валюты в формате ISO (USD, UAH)
     * @param \DateTime $date Если null - текущая дата
     *
     * @throws UndefinedCurrencyException
     * @return ExchangeRate
     */
    public function getRateExchange($code, \DateTime $date = null)
    {
        $rates = $this->getAllRatesExchanges($date);
        if (array_key_exists($code, $rates)) {
            return $rates[$code];
        }

        if ($this->showExceptions) {
            throw new UndefinedCurrencyException(sprintf('Undefined currency code: %s', $code));
        }

        return new ExchangeRate(null, null, null, null, null, null, new \DateTime());
    }


    /**
     * @param string $code
     * @param \DateTime $firstDate
     * @param \DateTime $lastDate
     *
     * @throws UndefinedCurrencyException
     * @return CurrencyRateDate[]
     */
    public function getRatesExchangesDynamic($code, \DateTime $firstDate, \DateTime $lastDate)
    {
        $currency = $this->getRateExchange($code);

        if (!$currency->getId()) {

            if ($this->showExceptions) {
                throw new UndefinedCurrencyException(sprintf('Undefined currency id: %s', $code));
            }

            return [];
        }

        try {
            $body = $this->apiClient->getXmlExchangesRatesDynamic($currency->getId(), $firstDate, $lastDate);
            $xml = simplexml_load_string($body);

            $result = [];
            if (count($xml->Record)) {
                foreach ($xml->Record as $item) {
                    $rate = CurrencyRateDate::createFromXML($item);
                    $result[] = $rate;
                }
            }

            return $result;

        } catch (\Exception $exc) {
            if ($this->showExceptions) {
                throw new $exc;
            }

            return [];
        }
    }

}