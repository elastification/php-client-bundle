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

class TransportCompilerPass implements CompilerPassInterface
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
        $config = $container->getParameter(ElastificationPhpClientExtension::PARAMETER_CONFIG_KEY);


        $typeDef = $this->createTransportTypeDefinition($container, $config);
        $this->createTransportDefinition($container, $config, $typeDef);
    }

    /**
     * creates a type definition nd adds it to container
     *
     * @param ContainerBuilder $container
     * @param array $config
     * @return Definition
     * @author Daniel Wendlandt
     */
    private function createTransportTypeDefinition(ContainerBuilder $container, array $config)
    {
        $transportTypeDef = new Definition();
        $transportTypeDef->setPublic(false);

        switch($config['protocol']) {
            case 'thrift':
                //todo implement factory here
                $transportTypeDef->setClass(
                    $container->getParameter('elastification_php_client.transport.thrift.type.class'));
                break;

            default:
                $transportTypeDef->setClass(
                    $container->getParameter('elastification_php_client.transport.http.type.class'));

                $baseUrl = 'http://' . $config['host'] . ':' . $config['port'] . '/';
                $transportTypeDef->setArguments(array(array('base_url' => $baseUrl)));
                break;
        }

        $container->setDefinition(ElastificationPhpClientExtension::SERVICE_TRANSPORT_TYPE_KEY, $transportTypeDef);

        return $transportTypeDef;
    }

    /**
     * creates a transport definitions based on config
     *
     * @param ContainerBuilder $container
     * @param array $config
     * @param Definition $typeDef
     * @return Definition
     * @author Daniel Wendlandt
     */
    private function createTransportDefinition(ContainerBuilder $container, array $config, Definition $typeDef)
    {
        $transportDef = new Definition();
        $transportDef->setPublic(false);

        switch($config['protocol']) {
            case 'thrift':
                $transportDef->setClass(
                    $container->getParameter('elastification_php_client.transport.thrift.class'));
                break;

            default:
                $transportDef->setClass(
                    $container->getParameter('elastification_php_client.transport.http.class'));

                break;
        }

        $transportDef->setArguments(array($typeDef));
        $container->setDefinition(ElastificationPhpClientExtension::SERVICE_TRANSPORT_KEY, $transportDef);

        return $transportDef;
    }
}