<?php

namespace BenderBundle\Service;

/**
 * Class Plugin
 */

abstract class BaseService
{
    protected $hook = "";
    protected $factory;
    public $cache;
    private $container;


    public function __construct(FactoryService $factory){
        $this->factory = $factory;
        $this->container = $this->factory->getContainer();
        $this->cache = $this->container->get('cache');
        $this->cache->setNamespace($this->getFactory()->getTeamDomain());
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer(){
        return $this->container;
    }

    /**
     * @return FactoryService
     */
    public function getFactory(){
        return $this->factory;
    }

    public function getUserName(){
        return $this->getFactory()->getUserName();
    }

    public function getChannelName(){
        return $this->getFactory()->getChannelName();
    }

    public function getText(){
        return $this->getFactory()->getText();
    }

    public function getToken(){
        return $this->getFactory()->getToken();
    }

    public function getTeamDomain(){
        return $this->getFactory()->getTeamDomain();
    }

    /**
     * Get Message
     * @param $text
     * @return mixed
     */
    abstract protected function getMessage($text);

    /**
     * Get Hook
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * @param     $arr
     * @param int $num
     * @return array
     */
    public function array_random($arr, $num = 1) {
        shuffle($arr);

        $r = array();
        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$i];
        }
        return $num == 1 ? $r[0] : $r;
    }

    /**
     * Get Commands
     * @param $text
     * @return array
     */
    public function getCommands($text)
    {
        $txt = explode(" ",$text);
        $commands = array();
        foreach($txt as $t)
        {
            if($t!=$this->hook)
            {
                $commands[]=$t;
            }
        }
        return $commands;
    }

    /**
     * Get Help
     * @return string
     */
    public function getHelp()
    {
        return $this->getHook();
    }

    /**
     * @param $url
     * @return mixed
     */
    public function get_page_content($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

}
