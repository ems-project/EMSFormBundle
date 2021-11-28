<?php

namespace EMS\FormBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class EMSFormExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @param mixed[] $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('emsf.hashcash.difficulty', $config[Configuration::HASHCASH_DIFFICULTY]);
        $container->setParameter('emsf.endpoints', $config[Configuration::ENDPOINTS]);
        $container->setParameter('emsf.ems_config', [
            Configuration::TYPE => $config['instance'][Configuration::TYPE],
            Configuration::TYPE_FORM_FIELD => $config['instance'][Configuration::TYPE_FORM_FIELD],
            Configuration::TYPE_FORM_MARKUP => $config['instance'][Configuration::TYPE_FORM_MARKUP],
            Configuration::TYPE_FORM_SUBFORM => $config['instance'][Configuration::TYPE_FORM_SUBFORM],
            Configuration::TYPE_FORM_CHOICE => $config['instance'][Configuration::TYPE_FORM_CHOICE],
            Configuration::FORM_FIELD => $config['instance'][Configuration::FORM_FIELD],
            Configuration::FORM_TEMPLATE_FIELD => $config['instance'][Configuration::FORM_TEMPLATE_FIELD],
            Configuration::THEME_FIELD => $config['instance'][Configuration::THEME_FIELD],
            Configuration::SUBMISSION_FIELD => $config['instance'][Configuration::SUBMISSION_FIELD],
            Configuration::DOMAIN => $config['instance'][Configuration::DOMAIN],
            Configuration::LOAD_FROM_JSON => $config['instance'][Configuration::LOAD_FROM_JSON],
        ]);
    }
}
