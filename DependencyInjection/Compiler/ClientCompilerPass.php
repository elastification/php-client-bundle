<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 05/01/15
 * Time: 11:48
 */

namespace Elastification\Bundle\ElastificationPhpClientBundle\DependencyInjection\Compiler;

use Elastification\Bundle\ElastificationPhpClientBundle\DependencyInjection\ElastificationPhpClientExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ClientCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $requestManagerDef = $container->getDefinition(ElastificationPhpClientExtension::SERVICE_REQUESTMANAGER_KEY);
        $transportDef = $container->getDefinition(ElastificationPhpClientExtension::SERVICE_TRANSPORT_KEY);

        $clientDef = new Definition(
            $container->getParameter('elastification_php_client.client.class'),
            array($transportDef, $requestManagerDef));

        $clientDef->setPublic(false);

        $container->setDefinition(ElastificationPhpClientExtension::SERVICE_CLIENT_KEY, $clientDef);

        $container->setAlias(
            ElastificationPhpClientExtension::ALIAS_CLIENT,
            ElastificationPhpClientExtension::SERVICE_CLIENT_KEY);
    }
}