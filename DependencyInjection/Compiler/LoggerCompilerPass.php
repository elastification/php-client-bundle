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

class LoggerCompilerPass implements CompilerPassInterface
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

        if(true === $config['logging_enabled']) {

            $container->setAlias(
                ElastificationPhpClientExtension::ALIAS_CLIENT,
                ElastificationPhpClientExtension::SERVICE_CLIENT_LOGGER_KEY);


        } else {
            $container->removeDefinition(ElastificationPhpClientExtension::SERVICE_CLIENT_LOGGER_KEY);
        }
    }
}