<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle;

/**
 * Курс обмена
 * Class ExchangeRate
 * @package Submarine\NbrbParserBundle
 */
class ExchangeRate
{

    /**
     * Внутренний код валюты
     * @var int
     */
    private $id;

    /**
     * цифровой код
     * @var int
     */
    private $numCode;

    /**
     * буквенный код
     * @var string
     */
    private $charCode;

    /**
     * номинал
     * @var int
     */
    private $scale;

    /**
     * наименование валюты
     * @var string
     */
    private $name;

    /**
     * курс
     * @var float
     */
    private $rate;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * ExchangeRate constructor.
     * @param int $id
     * @param int $numCode
     * @param string $charCode
     * @param int $scale
     * @param string $name
     * @param float $rate
     * @param \DateTime $date
     */
    public function __construct($id, $numCode, $charCode, $scale, $name, $rate, \DateTime $date)
    {
        $this->id = $id;
        $this->numCode = $numCode;
        $this->charCode = $charCode;
        $this->scale = $scale;
        $this->name = $name;
        $this->rate = $rate;
        $this->date = $date;
    }


    static public function createFromXML(\SimpleXMLElement $xmlElement = null, \DateTime $date)
    {
        return new ExchangeRate(
            (int)$xmlElement->attributes()[0],
            (int)$xmlElement->NumCode,
            (string)$xmlElement->CharCode,
            (int)$xmlElement->Scale,
            (string)($xmlElement->Name ?: $xmlElement->QuotName),
            (float)$xmlElement->Rate,
            $date
        );
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNumCode()
    {
        return $this->numCode;
    }


    /**
     * @return string
     */
    public function getCharCode()
    {
        return $this->charCode;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }


}