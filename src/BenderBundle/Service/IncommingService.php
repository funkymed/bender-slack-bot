<?php
//
//
////Add cron http://url/index.php?token=your_token&team_domain=your_team&channel_name=your_channel&incomming=1
//if($message===false && !empty($token) && !empty($team_domain) && !empty($channel_name) && !empty($incomming))
//{
//    $message = $classes['!lol']->getMessage("!lol random");
//
//    if($message)
//    {
//        $data = "payload=" . json_encode(array(
//                "channel"       =>  "#{$channel_name}",
//                "text"          =>  $message,
//                "username"      => "bender"
//                //"icon_emoji"    =>  $icon
//            ));
//
//        $ch = curl_init("https://".$team_domain.".slack.com/services/hooks/incoming-webhook?token=".$token);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $result = curl_exec($ch);
//        curl_close($ch);
//        $response = array('success'=>true);
//    }else{
//        $response = array('success'=>false);
//
//    }
////Response
//}elseif($user_id!='USLACKBOT' && $message!=false)
//{
//    if(is_array($message))
//        $message=implode("\n",$message);
//
//    $response = array('text'=>$message);
//}
//if(isset($response) && !(empty($response)))
//    echo json_encode($response);
