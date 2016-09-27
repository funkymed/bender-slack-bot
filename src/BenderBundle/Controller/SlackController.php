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
     * @Route("/oauth/slack")
     */
    public function authAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
        return new Response("ok");
    }
}
