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
        $cacheKey = 'submarine_nbrb_' . ($date !== null) ? $date->format('dmy') : 'now';

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
        return $this->provider->getRatesExchanges($codes, $date);
    }

    /**
     * {@inheritdoc}
     */
    public function getRateExchange($code, \DateTime $date = null)
    {
        return $this->provider->getRateExchange($code, $date);
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


}