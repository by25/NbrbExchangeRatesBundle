<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle\Tests\Provider;


use Submarine\NbrbExchangeRatesBundle\Client\ApiClient;
use Submarine\NbrbExchangeRatesBundle\Exception\UndefinedCurrencyException;
use Submarine\NbrbExchangeRatesBundle\ExchangeRate;
use Submarine\NbrbExchangeRatesBundle\Provider\ExchangeRatesProvider;
use Submarine\NbrbExchangeRatesBundle\Tests\Client\ApiClientTest;

class ExchangeRatesProviderTest extends \PHPUnit_Framework_TestCase
{

    private function getProvider()
    {
        $apiClient = new ApiClient(ApiClientTest::URL_RATES, ApiClientTest::URL_RATES_DYNAMIC, 3, 3);
        return new ExchangeRatesProvider($apiClient, true);
    }


    private $ratesList = [
        'USD' => [
            'code' => 'USD',
            'num_code' => 840,
            'scale' => 1,
            'name' => 'Доллар США',
            'rate' => 19958
        ],
        'EUR' => [
            'code' => 'EUR',
            'num_code' => 978,
            'scale' => 1,
            'name' => 'Евро',
            'rate' => 22747
        ],
        'PLN' => [
            'code' => 'PLN',
            'num_code' => 985,
            'scale' => 1,
            'name' => 'Злотый',
            'rate' => 5305.16
        ],
    ];

    private $dynamicRates = [
        'USD' => [
            '04/12/2016' => 19958,
            '04/13/2016' => 19864,
            '04/14/2016' => 19849,
            '04/15/2016' => 19989,
            '04/16/2016' => 19907,
        ]
    ];


    public function testGetAllRatesExchanges()
    {
        $provider = $this->getProvider();

        $date = new \DateTime();
        $date->setDate(2016, 4, 12);
        $rates = $provider->getAllRatesExchanges($date);

        foreach ($this->ratesList as $key => $value) {
            $rate = $rates[$key];
            $this->assertRate($rate, $date, $value);
        }
    }


    public function testGetRatesExchanges()
    {
        $provider = $this->getProvider();

        $date = new \DateTime();
        $date->setDate(2016, 4, 12);
        $rates = $provider->getRatesExchanges(['USD', 'PLN'], $date);
        foreach ($rates as $rate) {
            $pattern = $this->ratesList[$rate->getCharCode()];
            $this->assertRate($rate, $date, $pattern);
        }

        // Fail
        try {
            $provider->getRatesExchanges(['USD', 'HUI'], $date);
            $this->assertTrue(false);
        } catch (UndefinedCurrencyException $exc) {
            $this->assertTrue(true);
        }
    }


    public function testGetRateExchange()
    {
        $provider = $this->getProvider();

        $date = new \DateTime();
        $date->setDate(2016, 4, 12);
        $rate = $provider->getRateExchange('USD', $date);
        $this->assertRate($rate, $date, $this->ratesList['USD']);

        // Fail
        try {
            $provider->getRateExchange('HUI', $date);
            $this->assertTrue(false);
        } catch (UndefinedCurrencyException $exc) {
            $this->assertTrue(true);
        }
    }


    public function testGetRatesExchangesDynamic()
    {
        $provider = $this->getProvider();

        $start = new \DateTime();
        $start->setDate(2016, 4, 12);

        $end = new \DateTime();
        $end->setDate(2016, 4, 16);

        $code = 'USD';
        $rates = $provider->getRatesExchangesDynamic($code, $start, $end);

        foreach ($rates as $rate) {
            $date = $rate->getDate()->format('m/d/Y');

            $this->assertTrue(isset($this->dynamicRates[$code][$date]));
            $this->assertTrue($rate->getRate() == $this->dynamicRates[$code][$date]);
        }


    }


    private function assertRate(ExchangeRate $rate, \DateTime $date, $value)
    {
        $this->assertTrue($rate->getNumCode() === $value['num_code']);
        $this->assertTrue($rate->getScale() === $value['scale']);
        $this->assertTrue($rate->getName() === $value['name']);
        $this->assertTrue($rate->getRate() == $value['rate']);
        $this->assertTrue($rate->getCharCode() === $value['code']);
        $this->assertTrue($rate->getDate()->format('dmy') === $date->format('dmy'));
    }

}