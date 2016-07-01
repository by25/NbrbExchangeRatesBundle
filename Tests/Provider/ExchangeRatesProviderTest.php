<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle\Tests\Provider;


use PHPUnit\Framework\TestCase;
use Submarine\NbrbExchangeRatesBundle\Client\ApiClient;
use Submarine\NbrbExchangeRatesBundle\Exception\UndefinedCurrencyException;
use Submarine\NbrbExchangeRatesBundle\ExchangeRate;
use Submarine\NbrbExchangeRatesBundle\Provider\ExchangeRatesProvider;

class ExchangeRatesProviderTest extends TestCase
{

    const URL_RATES = 'http://www.nbrb.by/Services/XmlExRates.aspx';

    const URL_RATES_DYNAMIC = 'http://www.nbrb.by/Services/XmlExRatesDyn.aspx';

    private function getProvider()
    {
        $apiClient = new ApiClient(self::URL_RATES, self::URL_RATES_DYNAMIC, 3, 3);
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


    public function testDenominationGetRateExchanges()
    {
        $provider = $this->getProvider();

        $date = new \DateTime();
        $date->setDate(2016, 7, 1);
        $rate = $provider->getRateExchange('USD', $date);
        $this->assertEquals($rate->getRate(), 2.0053);

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
            $this->assertEquals($rate->getRate(), $this->dynamicRates[$code][$date]);
        }


    }


    private function assertRate(ExchangeRate $rate, \DateTime $date, $value)
    {
        $this->assertEquals($rate->getNumCode(), $value['num_code']);
        $this->assertEquals($rate->getScale(), $value['scale']);
        $this->assertEquals($rate->getName(), $value['name']);
        $this->assertEquals($rate->getRate(), $value['rate']);
        $this->assertEquals($rate->getCharCode(), $value['code']);
        $this->assertEquals($rate->getDate()->format('dmy'), $date->format('dmy'));
    }

}