<?php

namespace BenderBundle\Service;

use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\VarDumper\VarDumper;

class QRService extends BaseService
{
    /**
     * @var string
     */
    protected $hook = '!qr';
    private $qr = array();

    public function __construct(FactoryService $factory)
    {
        parent::__construct($factory);
        $this->qr = $this->getContainer()->getParameter("bender.qr");

//        VarDumper::dump($this->cache);

    }

    /**
     * @param       $text
     * @param array $array
     * @return bool|mixed
     */
    function getResponse($text, $array = array())
    {
        $user_name = $this->getUserName();

        $t  = explode (' ', $this->removeAccents($text));

        foreach($array as $k=>$v)
        {
            if(is_array($v) && isset($v['--response--']))
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

        $user_name = $this->getUserName();
        $session = $this->loadSession();

        // IF user is not the same in conversation
        if(isset($session['qr_user']) && $session['qr_user']!=$user_name)
            $this->clearSession();

        if(
          is_array($session) &&
          isset($session['qr_response']) &&
          (isset($session['qr_user']) && $session['qr_user']==$user_name)
        )
        {
            $response = $this->getResponse($text, $session['qr_response']);

        }else{
            $response = $this->getResponse($text, $this->qr);
        }

        if(!$response)
        {
            $this->clearSession();
            $response = $this->getResponse($text,$this->qr);
        }

        return $response;
    }

    /**
     * @return array
     */
    private function loadSession()
    {
        if($this->cache->contains($this->getKeyUser())){
            return $this->cache->fetch($this->getKeyUser());
        }
    }

    /**
     * @param $response
     */
    private function saveSession($response=false)
    {
        if($response)
        {
            $data = array(
              'qr_response'=>$response,
              'qr_user'=>$this->getUserName()
            );
            $this->cache->save($this->getKeyUser(),$data,500);
        }
    }

    /**
     * clear session
     */
    private function clearSession()
    {
        if($this->cache->contains($this->getKeyUser())){
            $this->cache->delete($this->getKeyUser());
        }
    }

    private function getKeyUser(){
        return 'qr_'.$this->getFactory()->getUserId();
    }

    /**
     * @param $str
     * @return mixed
     */
    private function removeAccents($str) {
        $a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ','Ά','ά','Έ','έ','Ό','ό','Ώ','ώ','Ί','ί','ϊ','ΐ','Ύ','ύ','ϋ','ΰ','Ή','ή');
        $b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o','Α','α','Ε','ε','Ο','ο','Ω','ω','Ι','ι','ι','ι','Υ','υ','υ','υ','Η','η');
        return str_replace($a, $b, $str);
    }

    /**
     * @return bool|string
     */
    public function getHelp()
    {
        return false;
    }
}
