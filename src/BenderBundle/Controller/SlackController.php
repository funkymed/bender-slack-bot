<?php

namespace BenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

class SlackController extends Controller
{

    /**
     * @Route("/oauth/authorize")
     */
    public function authAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
        return new Response("ok");
    }

    /**
     * @Route("/install")
     */
    public function installAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
        return new Response("install");
    }

    /**
     * @Route("/privacy")
     */
    public function privacyAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
        return new Response("privacy");
    }

    /**
     * @Route("/support")
     */
    public function supportAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
        return new Response("support");
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
