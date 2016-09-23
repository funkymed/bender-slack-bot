<?php

namespace BenderBundle;

use BenderBundle\DependencyInjection\BenderExtension;
use BenderBundle\DependencyInjection\Compiler\BenderServicesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BenderBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BenderServicesPass());
    }

    public function getContainerExtension()
    {
        return new BenderExtension();
    }
}
