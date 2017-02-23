<?php

namespace BenderBundle\Service;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FactoryService
 * @package BenderBundle\Service
 */
class FactoryService
{
    private $data = [];
    private $classes = [];
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $request = $container->get('request_stack')->getCurrentRequest();
        $keys    = array(
            'incomming','user_name','user_id','team_domain',
            'channel_name','trigger_word','token','text','timestamp'
        );

        foreach($keys as $v)
        {
            $this->data[$v] = $request->get($v,false);
        }
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * get Slack UserName
     * @return array|bool
     */
    public function getUserName()
    {
        return $this->getData('user_name');
    }

    /**
     * get Current ChannelName
     * @return array|bool
     */
    public function getChannelName()
    {
        return $this->getData('channel_name');
    }

    /**
     * Get Posted text
     * @return array|bool
     */
    public function getText()
    {
        return $this->getData('text');
    }

    /**
     * Get Current Slack Tocken
     * @return array|bool
     */
    public function getToken()
    {
        return $this->getData('token');
    }

    /**
     * Get TeamDomain
     * @return array|bool
     */
    public function getTeamDomain()
    {
        return $this->getData('team_domain');
    }

    /**
     * Get UserId
     * @return array|bool
     */
    public function getUserId()
    {
        return $this->getData('user_id');
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param bool $offset
     * @return array|bool
     */
    public function getData($offset=false)
    {
        if($offset){
            return isset($this->data[$offset]) ? $this->data[$offset] : false;
        }else{
            return $this->data;
        }
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    public function getResponseMessage()
    {
        $this->classes = $this->container->get('bender.services_chain')->getServices();
        $help = $this->container->get('bender.services_chain')->getHelps();

        $message = false;
        $text = $this->getText();

        if($this->getUserName() && $this->getUserName()=="slackbot")
            return;

        if(empty($text))
            return;

        if(empty($help))
            return;

        //Display HELP from plugin
        if(strstr(strtolower($text), "!help"))
        {
            $message = ["text"=>implode("\n",$help)];
            //Execute plugin if triggered
        }else{
            //Process Plugin
            foreach($this->classes as $k=>$class)
            {
                if(strstr($text, $k))
                {
                    $message = $class->getMessage($text);
                }
            }
            //If no message try something else funny
            //Take a look to the qr plugin
            if($message===false && isset($this->classes['!qr']))
            {
                $res = $this->classes['!qr']->getMessage($text);
                if($res)
                {
                    $message=$res;
                }
            }
        }

        if($this->getUserId()!='USLACKBOT' && $message!=false)
        {
            return $message;
        }else{
            return "";
        }
    }
}


