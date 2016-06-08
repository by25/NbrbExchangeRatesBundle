<?php

namespace Submarine\NbrbExchangeRatesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SubmarineNbrbExchangeRatesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Source
        $container->setParameter(
            'submarine_nbrb_exchange_rates.source.url_exchange_rates',
            $config['source']['url_exchange_rates']
        );

        $container->setParameter(
            'submarine_nbrb_exchange_rates.source.url_exchange_rates_dynamic',
            $config['source']['url_exchange_rates_dynamic']
        );

        $container->setParameter(
            'submarine_nbrb_exchange_rates.source.connect_timeout',
            $config['source']['connect_timeout']
        );

        $container->setParameter(
            'submarine_nbrb_exchange_rates.source.timeout',
            $config['source']['timeout']
        );

        // Other
        $container->setParameter(
            'submarine_nbrb_exchange_rates.exception',
            $config['exception']
        );

    }
}
