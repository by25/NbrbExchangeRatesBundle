<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle\Provider;


use Submarine\NbrbExchangeRatesBundle\CurrencyRateDate;
use Submarine\NbrbExchangeRatesBundle\ExchangeRate;

interface ExchangeRatesProviderInterface
{
    /**
     * Все курсы валют за указанную дату
     *
     * @param \DateTime $date Если null - текущая дата
     *
     * @return ExchangeRate[]
     */
    public function getAllRatesExchanges(\DateTime $date = null);

    /**
     * Курсы выбранных валют за указанную дату
     *
     * @param array $codes Коды валют в формате ISO (USD, UAH)
     * @param \DateTime $date Дата, по умолчанию текущая дата
     *
     * @return ExchangeRate[]
     */
    public function getRatesExchanges(array $codes, \DateTime $date = null);

    /**
     * Курс валюты за указанную дату
     *
     * @param string $code Код валюты в формате ISO (USD, UAH)
     * @param \DateTime $date Если null - текущая дата
     *
     * @return ExchangeRate
     */
    public function getRateExchange($code, \DateTime $date = null);

    /**
     * @param string $code
     * @param \DateTime $firstDate
     * @param \DateTime $lastDate
     *
     * @return CurrencyRateDate[]
     */
    public function getRatesExchangesDynamic($code, \DateTime $firstDate, \DateTime $lastDate);
}