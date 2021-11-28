<?php

namespace EMS\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const LOAD_FROM_JSON = 'load-from-json';
    public const SUBMISSION_FIELD = 'submission-field';
    public const THEME_FIELD = 'theme-field';
    public const FORM_TEMPLATE_FIELD = 'form-template-field';
    public const FORM_FIELD = 'form-field';
    public const TYPE_FORM_CHOICE = 'type-form-choice';
    public const TYPE_FORM_SUBFORM = 'type-form-subform';
    public const TYPE_FORM_MARKUP = 'type-form-markup';
    public const TYPE_FORM_FIELD = 'type-form-field';
    public const TYPE_FORM_VALIDATION = 'type-form_validation';
    public const TYPE = 'type';
    public const HASHCASH_DIFFICULTY = 'hashcash_difficulty';
    public const ENDPOINTS = 'endpoints';
    public const DOMAIN_FIELD = 'domain';
    public const CACHEABLE = 'cacheable';
    public const NAME_FIELD = 'name';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ems_form');
        /* @var $rootNode ArrayNodeDefinition */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->variableNode(self::HASHCASH_DIFFICULTY)->defaultValue(16384)->end()
                ->variableNode(self::ENDPOINTS)
                    ->defaultValue([])
                    ->example('[{"field_name":"send_confirmation","http_request":{"url":"https://api.example.test/v1/send/sms","headers":{"Content-Type":"application/json"},"body":"{\"To\": \"%value%\", \"Message\": \"%verification_code%\"}"}}]')
                ->end()
                ->arrayNode('instance')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode(self::TYPE)->defaultValue('form_instance')->end()
                        ->scalarNode(self::TYPE_FORM_FIELD)->defaultValue('form_structure_field')->end()
                        ->scalarNode(self::TYPE_FORM_MARKUP)->defaultValue('form_structure_markup')->end()
                        ->scalarNode(self::TYPE_FORM_SUBFORM)->defaultValue('form_structure')->end()
                        ->scalarNode(self::TYPE_FORM_CHOICE)->defaultValue('form_choice')->end()
                        ->scalarNode(self::TYPE_FORM_VALIDATION)->defaultValue('form_validation')->end()
                        ->scalarNode(self::FORM_FIELD)->defaultValue('form')->end()
                        ->scalarNode(self::FORM_TEMPLATE_FIELD)->defaultValue('form_template')->end()
                        ->scalarNode(self::THEME_FIELD)->defaultValue('theme_template')->end()
                        ->scalarNode(self::SUBMISSION_FIELD)->defaultValue('submissions')->end()
                        ->scalarNode(self::DOMAIN_FIELD)->defaultValue('domain')->end()
                        ->scalarNode(self::NAME_FIELD)->defaultValue('name')->end()
                        ->scalarNode(self::LOAD_FROM_JSON)->defaultValue(false)->end()
                        ->scalarNode(self::CACHEABLE)->defaultValue(true)->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
