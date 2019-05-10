<?php

namespace EMS\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        /* @var $rootNode ArrayNodeDefinition */
        $rootNode = $treeBuilder->root('ems_form');

        $rootNode
            ->children()
                ->arrayNode('instance')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')->defaultValue('form_instance')->end()
                        ->scalarNode('form-field')->defaultValue('form')->end()
                        ->scalarNode('theme-field')->defaultValue('theme_template')->end()
                    ->end()
                ->end()
                ->scalarNode('domain-type')->defaultValue('form_domain')->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}
