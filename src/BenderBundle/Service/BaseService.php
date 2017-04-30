<?php

namespace BenderBundle\Service;

use Symfony\Component\VarDumper\VarDumper;

/**
 * Class BaseService
 * @package BenderBundle\Service
 */
abstract class BaseService
{
    public $cache;
    public $badAnswer;
    public $color = "#00BBFF";
    protected $hook = "";
    protected $factory;
    private $container;

    /**
     * BaseService constructor.
     * @param FactoryService $factory
     */
    public function __construct(FactoryService $factory)
    {
        $this->factory = $factory;
        $this->container = $this->factory->getContainer();
        $this->cache = $this->container->get('cache');
        $this->cache->setNamespace($this->getFactory()->getTeamDomain());
        $this->badAnswer = $this->container->getParameter("bender.badanswer");
    }

    /**
     * @return FactoryService
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return array|bool
     */
    public function getUserName()
    {
        return $this->getFactory()->getUserName();
    }

    /**
     * @return array|bool
     */
    public function getChannelName()
    {
        return $this->getFactory()->getChannelName();
    }

    /**
     * @return array|bool
     */
    public function getText()
    {
        return $this->getFactory()->getText();
    }

    /**
     * @return array|bool
     */
    public function getToken()
    {
        return $this->getFactory()->getToken();
    }

    /**
     * @return array|bool
     */
    public function getTeamDomain()
    {
        return $this->getFactory()->getTeamDomain();
    }

    /**
     * @param     $arr
     * @param int $num
     * @return array
     */
    public function array_random($arr, $num = 1)
    {
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
        $txt = explode(" ", $text);
        $commands = array();
        foreach ($txt as $t) {
            if ($t != $this->hook) {
                $commands[] = $t;
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
     * Get Hook
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * @param $url
     * @return mixed
     */
    public function get_page_content($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    public function getMediaUrl($media)
    {
        $host = $this->getContainer()->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost();
        return $host . $media;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Get Message
     * @param $text
     * @return mixed
     */
    abstract protected function getMessage($text);

    /**
     * @param $message
     * @return array
     */
    protected function getAnswer($message)
    {
        if (is_array($message))
            $message = implode("\n", $message);
        return ["text" => $message];
    }

}
