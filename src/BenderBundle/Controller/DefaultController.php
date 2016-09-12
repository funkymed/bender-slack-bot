<?php

namespace BenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $message    = false;
        $keys       = array(
            'incomming','user_name','user_id','team_domain',
            'channel_name','trigger_word','token','text','timestamp'
        );

        foreach($keys as $v)
        {
            $$v = $request->get($v,false);
        }

        if(isset($user_name) && $user_name=="slackbot")
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
//            foreach($classes as $k=>$class)
//            {
//                if(strstr($text, $k))
//                {
//                    $message = $class->getMessage($text);
//                }
//            }
//            //If no message try something else funny
//            //Take a look to the qr plugin
//            if($message===false && isset($classes['!qr']))
//            {
//                $res = $classes['!qr']->getMessage($text);
//                if($res)
//                {
//                    $message=$res;
//                }
//            }
        }


        return new JsonResponse($message);
    }
}
