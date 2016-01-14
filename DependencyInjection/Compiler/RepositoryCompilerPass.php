<?php
/**
 * Created by PhpStorm.
 * User: dwendlandt
 * Date: 05/01/15
 * Time: 11:48
 */

namespace Elastification\Bundle\ElastificationPhpClientBundle\DependencyInjection\Compiler;

use Elastification\Bundle\ElastificationPhpClientBundle\DependencyInjection\ElastificationPhpClientExtension;
use Elastification\Client\ClientVersionMapInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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
        $this->modifySearchDefinition($container, $config, $classMapDef);
        $this->modifyIndexDefinition($container, $config, $classMapDef);
        $this->modifyCatDefinition($container, $config, $classMapDef);
    }

    /**
     * creates and registeres a classmap service definition
     *
     * @param ContainerBuilder $container
     * @param array $config
     * @return Definition
     * @author Daniel Wendlandt
     */
    private function createClassMapDefinition(ContainerBuilder $container, array $config)
    {
        $classMapDef = new Definition();
        $classMapDef->setPublic(false);
        $classMapDef->setClass($container->getParameter('elastification_php_client.repository.classmap.class'));
        $classMapDef->setArguments(array($config['elasticsearch_version']));

        $container->setDefinition(ElastificationPhpClientExtension::SERVICE_REPOSITORY_CLASSMAP, $classMapDef);

        return $classMapDef;
    }

    /**
     * modifies the arguments of the document repository service definition
     *
     * @param ContainerBuilder $container
     * @param array $config
     * @param Definition $classMapDef
     * @author Daniel Wendlandt
     */
    private function modifyDocumentDefinition(ContainerBuilder $container, array $config, Definition $classMapDef)
    {
        $documentDef = $container->getDefinition('elastification_php_client.repository.document');

        if(null !== $config['document_repository_serializer_dic_id']) {
            $documentDef->replaceArgument(1, new Reference($config['document_repository_serializer_dic_id']));
        }

        $documentDef->addArgument($classMapDef);
    }

    /**
     * modifies the arguments of the search repository service definition
     *
     * @param ContainerBuilder $container
     * @param array $config
     * @param Definition $classMapDef
     * @author Daniel Wendlandt
     */
    private function modifySearchDefinition(ContainerBuilder $container, array $config, Definition $classMapDef)
    {
        $searchDef = $container->getDefinition('elastification_php_client.repository.search');

        if(null !== $config['search_repository_serializer_dic_id']) {
            $searchDef->replaceArgument(1, new Reference($config['search_repository_serializer_dic_id']));
        }

        $searchDef->addArgument($classMapDef);
    }

    /**
     * modifies the arguments of the index repository service definition
     *
     * @param ContainerBuilder $container
     * @param array $config
     * @param Definition $classMapDef
     * @author Daniel Wendlandt
     */
    private function modifyIndexDefinition(ContainerBuilder $container, array $config, Definition $classMapDef)
    {
        $searchDef = $container->getDefinition('elastification_php_client.repository.index');

        if(null !== $config['index_repository_serializer_dic_id']) {
            $searchDef->replaceArgument(1, new Reference($config['index_repository_serializer_dic_id']));
        }

        $searchDef->addArgument($classMapDef);
    }

    /**
     * modifies the arguments of the cat repository service definition
     *
     * @param ContainerBuilder $container
     * @param array $config
     * @param Definition $classMapDef
     * @author Daniel Wendlandt
     */
    private function modifyCatDefinition(ContainerBuilder $container, array $config, Definition $classMapDef)
    {
        if(ClientVersionMapInterface::VERSION_V090X === $config['elasticsearch_version']) {
            throw new \InvalidArgumentException(sprintf('The cat repository is not available for the configured elasticsearch version'));
        }

        $searchDef = $container->getDefinition('elastification_php_client.repository.cat');

        if(null !== $config['index_repository_serializer_dic_id']) {
            $searchDef->replaceArgument(1, new Reference($config['index_repository_serializer_dic_id']));
        }

        $searchDef->addArgument($classMapDef);
    }
}