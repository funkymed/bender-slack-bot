<?php
/**
 * Class plugin_qr
 */
class plugin_qr extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!qr';

    public function __construct()
    {
        $this->qr = include "data/qr.php";
    }

    function getResponse($text, $array = false)
    {
        global $user_name;

        $array = ($array === false) ? $this->qr : $array;

        $t  = explode (' ',$text);

        foreach($array as $k=>$v)
        {
            if(isset($v['--response--']))
            {
                $response = $v['--response--'];
                unset($v['--response--']);
            }else{
                $response = false;
            }

            if(in_array(strtolower($text), explode(',',$k)))
            {
                if(is_array($v))
                {
                    $v = $this->array_random($v);
                }

                $msg = str_replace('%user_name', $user_name, $v);
                $msg = str_replace('%text', $text, $msg);

                $this->saveSession($response);

                return $msg;
            }

            foreach($t as $word)
            {
                if(in_array(strtolower($word), explode(',',$k)))
                {
                    if(is_array($v))
                        $v = $this->array_random($v);

                    $msg = str_replace('%user_name', $user_name, $v);
                    $msg = str_replace('%text', $text, $msg);

                    $this->saveSession($response);

                    return $msg;
                }
            }
        }
        return false;
    }

    /**
     * @param $text
     * @return bool|mixed
     */
    public function getMessage($text) {

        global $user_name;

        $session = $this->loadSession();

        //Si une autre personne parle on arrete le tracking
        if((isset($session['qr_user']) && $session['qr_user']!=$user_name))
        {
            $this->clearSession();
        }

        if(
          (isset($session['qr_response']) && is_array($session['qr_response'])) &&
          (isset($session['qr_user']) && $session['qr_user']==$user_name)
        )
        {
            $response = $this->getResponse($text,$session['qr_response']);
        }else{
            $response = $this->getResponse($text);
        }

        if($response===false)
            $this->clearSession();

        return $response;
    }

    private function getFilename()
    {
        global $team_domain;
        $path = dirname(__FILE__);
        $filename = $path.'/data/'.$team_domain.'_qr.txt';
        return $filename;
    }

    private function loadSession()
    {
        $filename = $this->getFilename();
        $res = @file_get_contents($filename);
        if($res)
        {
            $data  = unserialize($res);
            return is_array($data) ? $data : array();
        }else{
            return array();
        }
    }

    private function saveSession($response)
    {
        global $user_name;

        $filename = $this->getFilename();
        if($response)
        {
            $data = array(
              'qr_response'=>$response,
              'qr_user'=>$user_name
            );

            @file_put_contents($filename, serialize($data));
        }
    }

    private function clearSession()
    {
        $filename = $this->getFilename();
        @file_put_contents($filename, '');
    }

    /**
     * @return bool|string
     */
    public function getHelp()
    {
        return false;
    }
}

return new plugin_qr();