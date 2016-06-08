<?php

namespace Submarine\NbrbExchangeRatesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('submarine_nbrb_exchange_rates');

        $rootNode
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('source')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('url_exchange_rates')
                                ->defaultValue('http://www.nbrb.by/Services/XmlExRates.aspx')
                            ->end()
                            ->scalarNode('url_exchange_rates_dynamic')
                                ->defaultValue('http://www.nbrb.by/Services/XmlExRatesDyn.aspx')
                            ->end()
                            ->integerNode('connect_timeout')
                                ->min(0)->defaultValue(3)
                            ->end()
                            ->integerNode('timeout')
                                ->min(0)->defaultValue(3)
                            ->end()

                        ->end()
                    ->end()

                    ->booleanNode('exception')->defaultFalse()->end()

                ->end();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        return $treeBuilder;
    }
}
