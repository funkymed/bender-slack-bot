<?php

//Plugins
include "plugin.php";
$classes = array();
$help = array(
  "HELP :",
  "======",
);
foreach (glob("plugins/*.php") as $filename)
{
    $class = include $filename;

    $h = $class->getHelp();
    if($h)
        $help[]=$h;

    $classes[$class->getHook()] = $class;
}

//Request
$message    = false;
$keys       = array('user_name','user_id','team_domain','channel_name','trigger_word','text','timestamp');
foreach($keys as $v)
{
    $$v = isset($_REQUEST[$v]) ? $_REQUEST[$v] : false;
}

if(strstr($text, "!help"))
{
    $message = implode("\n",$help);
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
    if($message===false)
    {
        $res = $classes['!qr']->getMessage($text);
        if($res)
        {
            $message=$res;
        }
    }
}

//Response
if($user_id!='USLACKBOT' && $message!=false)
{
    $response = array('text'=>$message);
    echo json_encode($response);
}
