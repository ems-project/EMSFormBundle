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
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('emsf.domain.type', $config['domain-type']);
        $container->setParameter('emsf.instance.type', $config['instance']['type']);
        $container->setParameter('emsf.instance.form-field', $config['instance']['form-field']);
        $container->setParameter('emsf.instance.theme-field', $config['instance']['theme-field']);
    }
}
