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
                ->variableNode('endpoints')
                    ->defaultValue([])
                    ->example('[{"field_name":"send_confirmation","http_request":{"url":"https://api.example.test/v1/send/sms","headers":{"Content-Type":"application/json"},"body":"{\"To\": \"%value%\", \"Message\": \"%verification_code%\"}"}}]')
                ->end()
                ->arrayNode('instance')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')->defaultValue('form_instance')->end()
                        ->scalarNode('type-form-field')->defaultValue('form_structure_field')->end()
                        ->scalarNode('type-form-markup')->defaultValue('form_structure_markup')->end()
                        ->scalarNode('type-form-subform')->defaultValue('form_structure')->end()
                        ->scalarNode('type-form-choice')->defaultValue('form_choice')->end()
                        ->scalarNode('form-field')->defaultValue('form')->end()
                        ->scalarNode('form-template-field')->defaultValue('form_template')->end()
                        ->scalarNode('theme-field')->defaultValue('theme_template')->end()
                        ->scalarNode('submission-field')->defaultValue('submissions')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
