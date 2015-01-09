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

class JmsSerializerCompilerPass implements CompilerPassInterface
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
        if($container->hasAlias('jms_serializer')) {
            $config = $container->getParameter(ElastificationPhpClientExtension::PARAMETER_CONFIG_KEY);

            $container
                ->getDefinition('jms_serializer.handler_registry')
                ->addMethodCall('registerSubscribingHandler', array(new Reference('elastification_php_client.serializer.jms.sourcesubscribing')));

            $classMap = $this->formatClassMap($config);

            $this->createSerializer($container, $classMap, 'search');


        } else {
            $container->removeDefinition('elastification_php_client.serializer.jms.sourcesubscribing');
        }

        //remove prototype
        $container->removeDefinition('elastification_php_client.serializer.jms.prototype');

    }

    /**
     * formats the map
     *
     * @param array $config
     * @return array
     * @author Daniel Wendlandt
     */
    private function formatClassMap(array $config)
    {
        $classMap = array();

        if(isset($config['jms_serializer_class_map'])) {
            foreach($config['jms_serializer_class_map'] as $map) {
                if(isset($classMap[$map['index']])) {
                    $classMap[$map['index']][$map['type']] = $map['class'];
                } else {
                    $classMap[$map['index']] = array($map['type'] => $map['class']);
                }

            }
        }

        return $classMap;
    }

    /**
     * creates a serializer
     *
     * @param ContainerBuilder $container
     * @param array $classMap
     * @param string $name
     * @author Daniel Wendlandt
     */
    private function createSerializer(ContainerBuilder $container, array $classMap, $name)
    {
        $prototypeDef = $container->getDefinition('elastification_php_client.serializer.jms.prototype');

        $serializerDef = clone $prototypeDef;
        $serializerDef->setPublic(true);
        $serializerDef->addArgument(new Reference('elastification_php_client.serializer.jms.sourcesubscribing'));
        $serializerDef->addArgument($classMap);

        $container->setDefinition('elastification_php_client.serializer.jms.' . $name, $serializerDef);
    }
}