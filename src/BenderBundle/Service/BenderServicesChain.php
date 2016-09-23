<?php

namespace BenderBundle\Service;

class BenderServicesChain
{
    private $services;

    public function __construct()
    {
        $this->services = array();
    }

    public function addService(BaseService $service)
    {
        $this->services[$service->getHook()] = $service;
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getHelps()
    {
        $help    = ["HELP :", "======"];

        foreach ($this->services as $service)
        {
            $help[]=$service->getHelp();
        }
        return $help;
    }
}