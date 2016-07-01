<?php
/**
 * (c) itmedia.by <info@itmedia.by>
 */

namespace Submarine\NbrbExchangeRatesBundle\Provider;


use Doctrine\Common\Cache\Cache;

class CachedExchangeRateProvider implements ExchangeRatesProviderInterface
{

    /**
     * @var ExchangeRatesProviderInterface
     */
    private $provider;


    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var int
     */
    private $lifetime;


    /**
     * CachedExchangeRateProvider constructor.
     * @param ExchangeRatesProviderInterface $provider
     * @param Cache $cache
     * @param int $lifetime
     */
    public function __construct(ExchangeRatesProviderInterface $provider, Cache $cache, $lifetime = 10800)
    {
        $this->provider = $provider;
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }


    /**
     * {@inheritdoc}
     */
    public function getAllRatesExchanges(\DateTime $date = null)
    {
        $cacheKey = $this->createCacheKey('submarine_nbrb', $date);

        $result = $this->cache->fetch($cacheKey);
        if ($result) {
            return $result;
        }

        $result = $this->provider->getAllRatesExchanges($date);
        $this->cache->save($cacheKey, $result, $this->lifetime);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatesExchanges(array $codes, \DateTime $date = null)
    {
        $cacheKey = $this->createCacheKey('submarine_nbrb_' . implode('', $codes), $date);

        $result = $this->cache->fetch($cacheKey);
        if ($result) {
            return $result;
        }

        $result = $this->provider->getRatesExchanges($codes, $date);
        $this->cache->save($cacheKey, $result, $this->lifetime);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getRateExchange($code, \DateTime $date = null)
    {
        $cacheKey = $this->createCacheKey('submarine_nbrb_' . $code, $date);

        $result = $this->cache->fetch($cacheKey);
        if ($result) {
            return $result;
        }

        $result = $this->provider->getRateExchange($code, $date);
        $this->cache->save($cacheKey, $result, $this->lifetime);
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getRatesExchangesDynamic($code, \DateTime $firstDate, \DateTime $lastDate)
    {
        $cacheKey = 'submarine_nbrb_dyn_' . $firstDate->format('dmy') . $lastDate->format('dmy');

        $result = $this->cache->fetch($cacheKey);
        if ($result) {
            return $result;
        }

        $result = $this->provider->getRatesExchangesDynamic($code, $firstDate, $lastDate);
        $this->cache->save($cacheKey, $result, $this->lifetime);
        return $result;
    }


    /**
     * Ключ массива
     *
     * @param $name
     * @param \DateTime|null $date
     * @return string
     */
    private function createCacheKey($name, \DateTime $date = null)
    {
        $dateKey = ($date === null) ? date('dmy') : $date->format('dmy');
        return $name . '_' . $dateKey;
    }


}