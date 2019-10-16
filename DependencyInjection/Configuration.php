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
                ->variableNode('hashcash_difficulty')->defaultValue(16384)->end()
                ->arrayNode('instance')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')->defaultValue('form_instance')->end()
                        ->scalarNode('type-form-field')->defaultValue('form_structure_field')->end()
                        ->scalarNode('type-form-markup')->defaultValue('form_structure_markup')->end()
                        ->scalarNode('type-form-form')->defaultValue('form_structure')->end()
                        ->scalarNode('form-field')->defaultValue('form')->end()
                        ->scalarNode('theme-field')->defaultValue('theme_template')->end()
                        ->scalarNode('submission-field')->defaultValue('submissions')->end()
                    ->end()
                ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
}
