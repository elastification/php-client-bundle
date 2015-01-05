<?php

namespace Elastification\Bundle\ElastificationPhpClientBundle;

use Elastification\Bundle\ElastificationPhpClientBundle\DependencyInjection\Compiler\ClientCompilerPass;
use Elastification\Bundle\ElastificationPhpClientBundle\DependencyInjection\Compiler\TransportCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ElastificationPhpClientBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TransportCompilerPass());
        $container->addCompilerPass(new ClientCompilerPass());


//        $container->addCompilerPass(new DoctrineValidationPass('orm'));
//        $container->addCompilerPass(new EntityListenerPass());
    }
}
