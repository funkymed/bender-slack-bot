<?php

abstract class Plugin
{
    protected $hook = "";

    abstract protected function getMessage($text);

    public function getHook()
    {
        return $this->hook;
    }

    public function array_random($arr, $num = 1) {
        shuffle($arr);

        $r = array();
        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$i];
        }
        return $num == 1 ? $r[0] : $r;
    }

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

    public function getHelp()
    {
        return $this->getHook();
    }
}

