<?php

/*
 * By Cyril Pereira 2014
 */

//Plugins
include "plugin.php";
$classes = array();
$help    = array("HELP :", "======");
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

//Response
if($user_id!='USLACKBOT' && $message!=false)
{
    $response = array('text'=>$message);
    echo json_encode($response);
}
