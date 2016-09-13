<?php

namespace BenderBundle\Service;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FactoryService
 * @package BenderBundle\Service
 */
class FactoryService
{
    private $data = [];
    private $classes = [];

    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();
        $keys       = array(
            'incomming','user_name','user_id','team_domain',
            'channel_name','trigger_word','token','text','timestamp'
        );

        foreach($keys as $v)
        {
            $this->data[$v] = $request->get($v,false);
        }
    }

    public function getUserName(){
        return $this->getData('user_name');
    }

    public function getChannelName(){
        return $this->getData('channel_name');
    }

    public function getText(){
        return $this->getData('text');
    }

    public function getToken(){
        return $this->getData('token');
    }

    public function getTeamDomain(){
        return $this->getData('team_domain');
    }

    /**
     * @param $data
     */
    public function setData($data){
        $this->data = $data;
    }

    /**
     * @param bool $offset
     * @return array|bool
     */
    public function getData($offset=false){
        if($offset){
            return isset($this->data[$offset]) ? $this->data[$offset] : false;
        }else{
            return $this->data;
        }
    }

    /**
     * @param $classes
     */
    public function setClasses($classes){
        $this->classes = $classes;
    }

    /**
     * @return array
     */
    public function getClasses(){
        return $this->classes;
    }
}


