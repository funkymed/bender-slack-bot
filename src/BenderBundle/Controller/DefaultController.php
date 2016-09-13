<?php

namespace BenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\VarDumper;

class DefaultController extends Controller
{
    private $services = [
        'bender.debug','bender.allocine','bender.gif',
        'bender.lol','bender.meteo','bender.movie',
        'bender.qr','bender.quote','bender.recette',
        'bender.sondage','bender.wikipedia','bender.youtube',
    ];

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $factory = $this->get('bender.factory');
        $help    = ["HELP :", "======"];
        $classes = [];
        foreach ($this->services as $services)
        {
            $class = $this->get($services);
            if($class){
                $h = $class->getHelp();
                if($h)
                    $help[]=$h;
                $classes[$class->getHook()] = $class;
            }
        }

        $factory->setClasses($classes);
        $message    = false;
        $text = $factory->getText();

        if($factory->getUserName() && $factory->getUserName()=="slackbot")
            return new Response("");

        if(empty($text))
            return new Response("");

        if(empty($help))
            return new Response("");

        //Display HELP from plugin
        if(strstr(strtolower($text), "!help"))
        {
            $message = implode("\n",$help);
            //Execute plugin if triggered
        }else{
            //Process Plugin
            foreach($classes as $k=>$class)
            {
                if(strstr($text, $k))
                {
                    $message = $class->getMessage($text);
                }
            }
            //If no message try something else funny
            //Take a look to the qr plugin
            if($message===false && isset($classes['!qr']))
            {
                $res = $classes['!qr']->getMessage($text);
                if($res)
                {
                    $message=$res;
                }
            }
        }

        if(is_array($message))
            $message = implode("\n",$message);

        return new JsonResponse("{".$message."}");
    }
}
