<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle;


class CurrencyRateDate
{
    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var float
     */
    private $rate;

    /**
     * CurrencyRateDate constructor.
     * @param \DateTime $date
     * @param float $rate
     */
    public function __construct(\DateTime $date, $rate)
    {
        $this->date = $date;
        $this->rate = $rate;
    }


    static public function createFromXML(\SimpleXMLElement $xmlElement = null)
    {
        $dateArray = explode('/', (string)$xmlElement->attributes()[0]);
        if (count($dateArray) === 3) {
            $dateTime = new \DateTime();
            $dateTime->setDate($dateArray[2], $dateArray[0], $dateArray[1]);
            $dateTime->setTime(0, 0, 0);

            return new CurrencyRateDate($dateTime, (float)$xmlElement->Rate);
        }

        return new CurrencyRateDate(new \DateTime(), null);
    }


    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }


} 