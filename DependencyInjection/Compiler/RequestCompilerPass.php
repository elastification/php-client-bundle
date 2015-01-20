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
use Symfony\Component\DependencyInjection\Reference;

class RequestCompilerPass implements CompilerPassInterface
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
        $requestManagerDef = $container->getDefinition(ElastificationPhpClientExtension::SERVICE_REQUESTMANAGER_KEY);
        $taggedServices = $container->findTaggedServiceIds('elastification_php_client.request');

        foreach($taggedServices as $serviceId => $taggedParams) {

            $requestName = $serviceId;

            if(true === $config['replace_version_of_tagged_requests']) {
                $taggedRequestDef = $container->getDefinition($serviceId);
                $class = $taggedRequestDef->getClass();
                if(preg_match('/\\\\(V\d*x)\\\\/', $class, $requestVersion)) {

                    if($config['elasticsearch_version'] != $requestVersion[1]) {
                        $newClass = str_replace(
                            $requestVersion[0],
                            '\\' . $config['elasticsearch_version'] . '\\',
                            $class);

                        $taggedRequestDef->setClass($newClass);
                    }

                }
            }

            if(isset($taggedParams[0]['id'])) {
                $requestName = $taggedParams[0]['id'];
            }

            $requestManagerDef->addMethodCall('setRequest', array($requestName, new Reference($serviceId)));
        }

    }
}