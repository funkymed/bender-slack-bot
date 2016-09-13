<?php

namespace BenderBundle\Service;

/**
 * Class DebugService
 * @package BenderBundle\Service
 */
class DebugService extends BaseService
{
    protected $hook = '!debug';

    public function getHelp()
    {
        return '!debug';
    }

    public function getMessage($text)
    {
        $message=array();
        $message[]='===========';
        $message[]='Variables :';
        $message[]='===========';
        $data = $this->getFactory()->getData();
        foreach($data as $k=>$v)
        {
            $message[]=$k." : ".$v;
        }

        $message[]='================';
        $message[]='Plugins loaded : '.count(glob("plugins/*.php"));
        $message[]='================';
        $classes = $this->getFactory()->getClasses();
        foreach($classes as $k=>$c)
        {
            $message[]=$k." : ".get_class($c);
        }

        $message[]='================';
        $message[]='Data files : '.count(glob("data/*"));
        $message[]='================';
        $message[]='Done.';

        return $message;
    }

}
