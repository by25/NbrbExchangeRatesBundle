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


    public function __construct(\SimpleXMLElement $xmlElement = null)
    {
        $this->rate = (float)$xmlElement->Rate;

        $date = explode('/', (string)$xmlElement->attributes()[0]);
        if (count($date) == 3) {
            $dateTime = new \DateTime();
            $dateTime->setDate($date[2], $date[0], $date[1]);
            $dateTime->setTime(0, 0, 0);

            $this->date = $dateTime;
        }
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