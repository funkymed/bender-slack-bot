<?php

namespace BenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

class InterractiveController extends Controller
{

    /**
     * @Route("/message_action")
     */
    public function indexAction(Request $request)
    {
        $callback_id = $request->get('callback_id',false);
    }
}
