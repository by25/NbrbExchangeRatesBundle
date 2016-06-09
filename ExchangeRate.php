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

    public function __construct(\SimpleXMLElement $xmlElement = null, \DateTime $date = null)
    {
        if (!is_null($xmlElement)) {
            $this->id = (int)$xmlElement->attributes()[0];
            $this->numCode = (int)$xmlElement->NumCode;
            $this->charCode = (string)$xmlElement->CharCode;
            $this->name = (string)($xmlElement->Name ? $xmlElement->Name : $xmlElement->QuotName);
            $this->scale = (int)$xmlElement->Scale;
            $this->rate = (float)$xmlElement->Rate;
        }

        if ($date) {
            $this->date = $date;
        }
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