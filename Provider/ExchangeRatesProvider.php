<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle\Provider;

use Doctrine\Common\Cache\Cache;
use Submarine\NbrbExchangeRatesBundle\CurrencyRateDate;
use Submarine\NbrbExchangeRatesBundle\ExchangeRate;

/**
 * Class ExchangeRatesProvider
 * @package Submarine\NbrbParserBundle
 */
class ExchangeRatesProvider
{
    /**
     * @var Cache
     */
    private $cacheProvider;

    /**
     * Включем кэш?
     * @var bool
     */
    private $cacheEnabled = true;

    /**
     * Время жизни кэша, секунд
     * @var int
     */
    private $cacheLifeTime = 10800;

    /**
     * Наименование валюты, содержащее номинал
     * @var bool
     */
    private $scaledName = true;

    /**
     * Выкидывать исключения?
     * @var bool
     */
    private $showExceptions = true;


    /**
     * @var ApiClient
     */
    private $apiClient;


    // ------------------ Config -----------------

    /**
     * @param ApiClient $apiClient
     */
    public function setApiClient(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @param Cache $cacheProvider
     */
    public function setCacheProvider(Cache $cacheProvider)
    {
        $this->cacheProvider = $cacheProvider;
    }

    /**
     * @param boolean $cacheEnabled
     */
    public function setCacheEnabled($cacheEnabled)
    {
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * @param int $cacheLifeTime
     */
    public function setCacheLifeTime($cacheLifeTime)
    {
        $this->cacheLifeTime = $cacheLifeTime;
    }

    /**
     * @param boolean $scaledName
     */
    public function setScaledName($scaledName)
    {
        $this->scaledName = $scaledName;
    }

    /**
     * @param boolean $showExceptions
     */
    public function setShowExceptions($showExceptions)
    {
        $this->showExceptions = $showExceptions;
    }





    // ------------------ API ---------------

    /**
     * Все курсы валют за указанную дату
     * @param \DateTime $date Дата, по умолчанию текущая дата
     * @return ExchangeRate[]
     */
    public function getAllRatesExchanges(\DateTime $date = null)
    {
        if (!$date) {
            $date = new \DateTime();
        }

        try {
            $cacheKey = 'submarine_nbrb_parser_rates_' . $date->format('dmy');

            if ($this->cacheEnabled) {
                $result = $this->cacheProvider->fetch($cacheKey);
            }

            if (empty($result)) {

                $xml = simplexml_load_string(
                    $this->apiClient->getXmlExchangesRates($date, $this->scaledName)
                );

                $result = [];
                if (count($xml->Currency)) {
                    foreach ($xml->Currency as $item) {
                        $exRate = new ExchangeRate($item, $date);
                        $result[$exRate->getCharCode()] = $exRate;
                    }
                }

                if ($this->cacheEnabled and $result) {
                    $this->cacheProvider->save($cacheKey, $result, $this->cacheLifeTime);
                }

            }

        } catch (\Exception $exc) {
            if ($this->showExceptions) {
                throw new $exc;
            }
            return [];
        }

        return $result;
    }


    /**
     * Курсы выбранных валют за указанную дату
     * @param array $codes Коды валют в формате ISO (USD, UAH)
     * @param \DateTime $date Дата, по умолчанию текущая дата
     * @return ExchangeRate[]
     */
    public function getRatesExchanges(array $codes, \DateTime $date = null)
    {
        $rates = $this->getAllRatesExchanges($date);
        $result = [];
        foreach ($codes as $code) {
            if (isset($rates[$code])) {
                $result[$code] = $rates[$code];
            }
        }

        return $result;
    }


    /**
     * Курс валюты за указанную дату
     * @param string $code Код валюты в формате ISO (USD, UAH)
     * @param \DateTime $date
     * @return ExchangeRate
     */
    public function getRateExchange($code, \DateTime $date = null)
    {
        $rates = $this->getAllRatesExchanges($date);
        return isset($rates[$code]) ? $rates[$code] : new ExchangeRate();
    }


    /**
     * @param string $code
     * @param \DateTime $firstDate
     * @param \DateTime $lastDate
     * @return array|mixed
     */
    public function getRatesExchangesDynamic($code, \DateTime $firstDate, \DateTime $lastDate)
    {

        $currency = $this->getRateExchange($code);
        if (!$currency->getId()) {
            return [];
        }

        $cacheKey = md5('submarine_nbrb_parser_rates_' . $code . '_' . $firstDate->format('dmy') . '_' . $lastDate->format('dny'));

        if ($this->cacheEnabled) {
            $result = $this->cacheProvider->fetch($cacheKey);
        }

        try {
            if (empty($result)) {

                $xml = simplexml_load_string(
                    $this->apiClient->getXmlExchangesRatesDynamic($currency->getId(), $firstDate, $lastDate)
                );

                $result = [];
                if (count($xml->Record)) {
                    foreach ($xml->Record as $item) {
                        $rate = new CurrencyRateDate($item);
                        $result[] = $rate;
                    }
                }

                if ($this->cacheEnabled and $result) {
                    $this->cacheProvider->save($cacheKey, $result, $this->cacheLifeTime);
                }

            }

        } catch (\Exception $exc) {
            if ($this->showExceptions) {
                throw new $exc;
            }
            return [];
        }

        return $result;

    }

}