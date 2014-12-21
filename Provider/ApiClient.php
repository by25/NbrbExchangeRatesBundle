<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle\Provider;

use GuzzleHttp\Client;


class ApiClient
{

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
     * @param string $urlExchangeRates
     */
    public function setUrlExchangeRates($urlExchangeRates)
    {
        $this->urlExchangeRates = $urlExchangeRates;
    }

    /**
     * @param string $urlExchangeRatesDynamic
     */
    public function setUrlExchangeRatesDynamic($urlExchangeRatesDynamic)
    {
        $this->urlExchangeRatesDynamic = $urlExchangeRatesDynamic;
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
     * @param \DateTime $date
     * @param bool $quotName
     * @return string
     */
    public function getXmlExchangesRates(\DateTime $date, $quotName = true)
    {
        $query = [];
        $query['ondate'] = $date->format('m/d/Y');
        if ($quotName === true) {
            $query['mode'] = 1;
        }

        $response = $this->getClient()->get($this->urlExchangeRates, [
            'query' => $query
        ]);

        return $response->getBody()->read($response->getBody()->getSize());
    }


    /**
     * Динамика курса
     * @param $currencyId
     * @param \DateTime $firstDate
     * @param \DateTime $lastDate
     * @return string
     */
    public function getXmlExchangesRatesDynamic($currencyId, \DateTime $firstDate, \DateTime $lastDate)
    {
        $response = $this->getClient()->get($this->urlExchangeRatesDynamic, [
            'query' => [
                'curId' => $currencyId,
                'fromDate' => $firstDate->format('m/d/Y'),
                'toDate' => $lastDate->format('m/d/Y'),
            ]
        ]);

        return $response->getBody()->read($response->getBody()->getSize());
    }

}