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
        $help[]='__________                   .___            ';
        $help[]='\______   \ ____   ____    __| _/__________  ';
        $help[]='|    |  _// __ \ /    \  / __ |/ __ \_  __ \ ';
        $help[]='|    |   \  ___/|   |  \/ /_/ \  ___/|  | \/ ';
        $help[]='|______  /\___  >___|  /\____ |\___  >__|    ';
        $help[]='       \/     \/     \/      \/    \/        ';
        $help[]='  _________.__                 __     __________        __    ';
        $help[]=' /   _____/|  | _____    ____ |  | __ \______   \ _____/  |_  ';
        $help[]=' \_____  \ |  | \__  \ _/ ___\|  |/ /  |    |  _//  _ \   __\ ';
        $help[]=' /        \|  |__/ __ \\  \___|    <   |    |   (  <_> )  |   ';
        $help[]='/_______  /|____(____  /\___  >__|_ \  |______  /\____/|__|\  ';
        $help[]='        \/           \/     \/     \/         \/              ';


        foreach ($this->services as $service)
        {
            $help[]=$service->getHelp();
        }
        return $help;
    }
}