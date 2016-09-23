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
        $help=[];
        $help[]='```';
        $help[]='    __                   __                ';
        $help[]='   / /_  ___  ____  ____/ /__  _____       ';
        $help[]='  / __ \/ _ \/ __ \/ __  / _ \/ ___/       ';
        $help[]=' / /_/ /  __/ / / / /_/ /  __/ /           ';
        $help[]='/_.___/\___/_/ /_/\__,_/\___/_/ slack-bot  ';
        $help[]='```';

        foreach ($this->services as $service)
        {
            $help[]=$service->getHelp();
        }
        return $help;
    }
}