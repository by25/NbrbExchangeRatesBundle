<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle\Client;

use GuzzleHttp\Client;


class ApiClient
{

    const CACHE_KEY = 'nbrb-xml-cache';


    /**
     * Получение официального курса белорусского рубля по отношению к иностранным валютам на определенную дату
     *
     * @var string
     */
    private $urlExchangeRates = 'http://www.nbrb.by/Services/XmlExRates.aspx';

    /**
     * Получение динамики официального курса белорусского рубля по отношению к заданной иностранной валюте,
     * устанавливаемого Национальным банком Республики Беларусь (не более чем за 365 дней):
     *
     * @var string
     */
    private $urlExchangeRatesDynamic = 'http://www.nbrb.by/Services/XmlExRatesDyn.aspx';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var int
     */
    private $httpConnectTimeout;

    /**
     * @var int
     */
    private $httpTimeout;

    /**
     * ApiClient constructor.
     * @param string $urlExchangeRates
     * @param string $urlExchangeRatesDynamic
     * @param int $httpConnectTimeout
     * @param int $httpTimeout
     */
    public function __construct($urlExchangeRates, $urlExchangeRatesDynamic, $httpConnectTimeout, $httpTimeout)
    {
        $this->urlExchangeRates = $urlExchangeRates;
        $this->urlExchangeRatesDynamic = $urlExchangeRatesDynamic;
        $this->httpConnectTimeout = $httpConnectTimeout;
        $this->httpTimeout = $httpTimeout;
    }


    /**
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }


    /**
     * Курсы валют за дату
     *
     * @param \DateTime $date
     * @param bool $quotName
     *
     * @return string
     */
    public function getXmlExchangesRates(\DateTime $date, $quotName = true)
    {

        $query = [
            'ondate' => $date->format('m/d/Y')
        ];

        if ($quotName === true) {
            $query['mode'] = 1;
        }

        return $this->getResponseBody($this->urlExchangeRates, $query);
    }


    /**
     * Динамика курса
     *
     * @param $currencyId
     * @param \DateTime $firstDate
     * @param \DateTime $lastDate
     *
     * @return string
     */
    public function getXmlExchangesRatesDynamic($currencyId, \DateTime $firstDate, \DateTime $lastDate)
    {
        $query = [
            'curId' => $currencyId,
            'fromDate' => $firstDate->format('m/d/Y'),
            'toDate' => $lastDate->format('m/d/Y'),
        ];

        return $this->getResponseBody($this->urlExchangeRatesDynamic, $query);
    }


    /**
     * Возвращает XML документ
     * @param $url
     * @param $query
     * @return string
     */
    private function getResponseBody($url, $query)
    {
        $options['query'] = $query;
        $options['connect_timeout'] = $this->httpConnectTimeout;
        $options['timeout'] = $this->httpTimeout;

        $response = $this->getClient()->get($url, $options);

        return $response->getBody()->read($response->getBody()->getSize());
    }

}