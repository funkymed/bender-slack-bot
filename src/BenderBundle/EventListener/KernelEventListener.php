<?php

namespace BenderBundle\EventListener;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\VarDumper\VarDumper;

class KernelEventListener
{
    private $container;
    private $request;
    private $session;
    private $router;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->session = $container->get('session');
        $this->router = $container->get('router');
        $this->request = $container->get('request_stack')->getCurrentRequest();
    }

    public function onKernelRequest(Event $event)
    {
        $code = '21000909651.81942785152.418de98010';//$this->session->get('code',false);
        if (!$code)
            return true;

        //https://slack.com/oauth/pick_reflow?client_id=21000909651.81082015155&scope=chat:write:bot

        $client_id = $this->container->getParameter('slack_id');
        $secret = $this->container->getParameter('slack_secret');
        $client = new Client();

        try {
            $result = $client->request("POST", "https://slack.com/api/oauth.access", [
                'client_id' => $client_id,
                'client_secret' => $secret,
                'code' => $code,
            ]);

            $json = $result->getBody()->getContents();
            $json = \GuzzleHttp\json_decode($json);
//            VarDumper::dump($json);exit;
        } catch (ClientException $e) {
            return $this->array_random($this->badAnswer);
        }

//        https://slack.com/api/oauth.access
        return true;

    }


}