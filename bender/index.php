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
$keys       = array('incomming','user_name','user_id','team_domain','channel_name','trigger_word','token','text','timestamp');
foreach($keys as $v)
{
    $$v = isset($_REQUEST[$v]) ? $_REQUEST[$v] : false;
}

if($user_name=="slackbot")
    exit;

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

// send a !lol on channel from cron

//Add cron http://url/index.php?token=your_token&team_domain=your_team&channel_name=your_channel&incomming=1
if($message===false && !empty($token) && !empty($team_domain) && !empty($channel_name) && !empty($incomming))
{
    $message = $classes['!lol']->getMessage("!lol random");

    if($message)
    {
        $data = "payload=" . json_encode(array(
            "channel"       =>  "#{$channel_name}",
            "text"          =>  $message,
            "username"      => "bender"
              //"icon_emoji"    =>  $icon
          ));

        $ch = curl_init("https://".$team_domain.".slack.com/services/hooks/incoming-webhook?token=".$token);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = array('success'=>true);
    }else{
        $response = array('success'=>false);

    }
//Response
}elseif($user_id!='USLACKBOT' && $message!=false)
{
    if(is_array($message))
        $message=implode("\n",$message);

    $response = array('text'=>$message);
}
if(isset($response) && !(empty($response)))
    echo json_encode($response);
