<?php

namespace BenderBundle;

use BenderBundle\DependencyInjection\BenderExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BenderBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new BenderExtension();
    }
}
