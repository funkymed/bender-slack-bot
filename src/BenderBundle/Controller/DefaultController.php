<?php

namespace BenderBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @Route("/test")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template(":default:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
    }

    /**
     * @Route("/install")
     * @Template(":default:install.html.twig")
     */
    public function installAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
    }

    /**
     * @Route("/privacy")
     * @Template(":default:privacy.html.twig")
     */
    public function privacyAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
    }

    /**
     * @Route("/support")
     * @Template(":default:support.html.twig")
     */
    public function supportAction(Request $request)
    {
        $code = $request->get('code');
        $this->get('session')->set('code', $code);
    }
}
