<?php

namespace Kijho\MailerBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('kijho_mailer');

        $rootNode
            ->children()
                ->arrayNode('entity_directories')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->children()
                ->scalarNode('entity_namespace')
                ->end()
            ->end()
            ->children()
                ->arrayNode('storage')
                    ->children()
                        ->scalarNode('layout')->end()
                        ->scalarNode('template_group')->end()
                        ->scalarNode('template')->end()
                        ->scalarNode('settings')->end()
                        ->scalarNode('email')->end()
                        ->scalarNode('email_event')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
