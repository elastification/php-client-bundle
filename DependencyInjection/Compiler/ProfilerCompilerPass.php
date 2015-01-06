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
use Symfony\Component\DependencyInjection\Reference;

class ProfilerCompilerPass implements CompilerPassInterface
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


        if(true === $config['profiler_enabled']) {

            $clientAlias = $container->getAlias(ElastificationPhpClientExtension::ALIAS_CLIENT);
            $profilerDef = $container->getDefinition(ElastificationPhpClientExtension::SERVICE_CLIENT_PROFILER_KEY);

            $profilerDef->replaceArgument(0, new Reference($clientAlias->__toString()));

            $dataCollectorDef = $container->getDefinition('elastification_php_client.datacollector');
            $dataCollectorDef->addMethodCall('setConfig', array($config));

            $container->setAlias(
                ElastificationPhpClientExtension::ALIAS_CLIENT,
                ElastificationPhpClientExtension::SERVICE_CLIENT_PROFILER_KEY);


        } else {

            $sfProfilerDef = $container->getDefinition('profiler');
            $sfProfilerMethodCalls = $sfProfilerDef->getMethodCalls();
            foreach($sfProfilerMethodCalls as $sfProfilerMethodCallsIndex => $sfProfilerMethodCall) {
                if('elastification_php_client.datacollector' == $sfProfilerMethodCall[1][0]->__toString()) {
                    unset($sfProfilerMethodCalls[$sfProfilerMethodCallsIndex]);
                }
            }
            $sfProfilerDef->setMethodCalls($sfProfilerMethodCalls);


            $container->removeDefinition(ElastificationPhpClientExtension::SERVICE_CLIENT_PROFILER_KEY);
            $container->removeDefinition('elastification_php_client.datacollector');
        }
    }
}