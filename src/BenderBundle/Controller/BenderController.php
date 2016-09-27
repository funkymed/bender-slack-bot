<?php

namespace BenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @Route("/")
 */
class BenderController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $factory = $this->get('bender.factory');
        $message = $factory->getResponseMessage();

        if(empty($message)) {
            return new Response("");
        }else{
            return new JsonResponse($message);
        }
    }

    /**
     * @Route("/message_action")
     */
    public function messageAction(Request $request)
    {
        $callback_id = $request->get('callback_id',false);
        return new Response("ok");
    }
}
