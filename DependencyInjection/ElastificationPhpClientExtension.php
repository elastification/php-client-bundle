<?php

namespace Elastification\Bundle\ElastificationPhpClientBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ElastificationPhpClientExtension extends Extension
{

    const PARAMETER_CONFIG_KEY = 'elastification_php_client.config';

    const SERVICE_REQUESTMANAGER_KEY = 'elastification_php_client.requestmanager';
    const SERVICE_TRANSPORT_KEY = 'elastification_php_client.transport';
    const SERVICE_TRANSPORT_TYPE_KEY = 'elastification_php_client.transport.type';
    const SERVICE_CLIENT_KEY = 'elastification_php_client.client';

    const ALIAS_CLIENT = 'elastification_php_client';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter(self::PARAMETER_CONFIG_KEY, $config);
    }

}
