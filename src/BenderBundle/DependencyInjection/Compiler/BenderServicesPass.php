<?php
namespace BenderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class BenderServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('bender.services_chain')) {
            return;
        }

        $definition = $container->findDefinition('bender.services_chain');

        //Get All services with the good tag
        $taggedServices = $container->findTaggedServiceIds('bender.services');

        foreach ($taggedServices as $id => $tags) {
            // add the transport service to the ChainTransport service
            $definition->addMethodCall('addService', array(new Reference($id)));
        }
    }
}