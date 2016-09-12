<?php
/**
 * Class plugin_debug
 */

class plugin_debug extends Plugin
{
    protected $hook = '!debug';

    public function getHelp()
    {
        return '!debug';
    }

    public function getMessage($text)
    {
        global $classes;
        $keys = array('user_name','team_domain','channel_name','text','timestamp');
        $message=array();
        $message[]='===========';
        $message[]='Variables :';
        $message[]='===========';
        foreach($keys as $k)
        {
            global $$k;
            $message[]=$k." : ".$$k;
        }
        $message[]='================';
        $message[]='Plugins loaded : '.count(glob("plugins/*.php"));
        $message[]='================';
        foreach($classes as $k=>$c)
        {
            $message[]=$k." : ".get_class($c);
        }

        $message[]='================';
        $message[]='Data files : '.count(glob("plugins/data/*"));
        $message[]='================';
        $message[]='Done.';

        return $message;
    }

}

return new plugin_debug();