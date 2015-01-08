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

class RepositoryCompilerPass implements CompilerPassInterface
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


        $classMapDef = $this->createClassMapDefinition($container, $config);
        $this->modifyDocumentDefinition($container, $config, $classMapDef);
    }

    private function createClassMapDefinition(ContainerBuilder $container, array $config)
    {
        $classMapDef = new Definition();
        $classMapDef->setPublic(false);
        $classMapDef->setClass($container->getParameter('elastification_php_client.repository.classmap.class'));
        $classMapDef->setArguments(array($config['elasticsearch_version']));

        $container->setDefinition(ElastificationPhpClientExtension::SERVICE_REPOSITORY_CLASSMAP, $classMapDef);

        return $classMapDef;
    }

    private function modifyDocumentDefinition(ContainerBuilder $container, array $config, Definition $classMapDef)
    {
        $documentDef = $container->getDefinition('elastification_php_client.repository.document');

        if(null !== $config['repository_serializer_dic_id']) {
            $serializerDef = $container->getDefinition($config['repository_serializer_dic_id']);
            $documentDef->replaceArgument(1, $serializerDef);
        }

        $documentDef->addArgument($classMapDef);
    }
}