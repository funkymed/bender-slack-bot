<?php

/**
 * Class plugin_sondage
 */
class plugin_sondage extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!sondage';

    protected $sondage = array(
        'question'=>'',
        'date'=>'',
        "author"=>'',
        'choices'=>array(),
        'votes'=>array(),
        'open'=>false
    );

    /**
     * @var array
     */
    private $badAnswer = array(
      'Pas sûr que se soit ça.',
      "T'es sûr de ça ?",
      "Patate !",
    );

    public function getHelp()
    {
        return '!sondage start question/choix1,choix2,...|stop|restore|info|votes';
    }

    /**
     * @return mixed
     */
    public function getSondage()
    {
        global $team_domain;
        $path = dirname(__FILE__);
        $filename = $path.'/data/'.$team_domain.'_sondage.txt';

        if (file_exists($filename)) {

            $sondage = @file_get_contents($filename);
            return $sondage && $sondage!='' ? unserialize($sondage) : array();
        }else{
            @file_put_contents($filename, serialize(array()));
            return array();
        }
    }

    /**
     * @param $sondage
     * @return bool
     */
    public function save($sondage)
    {
        global $team_domain;

        $path = dirname(__FILE__);
        $res  = @file_put_contents($path.'/data/'.$team_domain.'_sondage.txt', serialize($sondage));

        return $res ? true : false;
    }

    private function getPourcentage($choices)
    {
        $total = count($this->sondage['votes']);
        if($total>0)
        {
            $count = 0;
            foreach($this->sondage['votes'] as $user=>$c)
            {
                if($c==$choices)
                    $count++;
            }

            return $count/$total*100;
        }else{
            return 0;
        }
    }

    private function getInfo()
    {
        if($this->isSondageStarted())
        {
            $nbVotes    = count($this->sondage['votes']);
            $output     = array();
            $output[]   = "Sondage commencé le ".$this->sondage['date']." par ".$this->sondage['author'];
            $output[]   = $this->sondage['question']." ".$nbVotes." vote".($nbVotes>1 ? 's' : '');

            foreach($this->sondage['choices'] as $choices)
            {
                $output[] = "- ".$choices.' ('.$this->getPourcentage($choices).'%)';
            }
            return implode("\n",$output);
        }else{
            return 'Pas de sondage en cours';
        }
    }

    /**
     * @return bool
     */
    private function isSondageStarted()
    {
        return (isset($this->sondage['open']) && $this->sondage['open']) ? true : false;
    }

    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text)
    {
        global $user_name;

        $current_sondage = $this->getSondage();
        if($current_sondage)
        {
            $this->sondage = $current_sondage;
        }

        $commands = $this->getCommands($text);
        if(isset($commands[0]))
        {
            switch($commands[0])
            {
                case "start":
                    if($this->isSondageStarted())
                    {
                        return 'Sondage en cours';
                    }

                    unset($commands[0]);
                    $tmp = implode(' ',$commands);
                    $tmp = explode('/',$tmp);
                    $this->sondage['date'] = date('d/m/Y \à H:i:s');
                    $this->sondage['question'] = $tmp[0];
                    $this->sondage['choices'] = explode(',',$tmp[1]);
                    $this->sondage['author'] = $user_name;
                    $this->sondage['votes'] = array();

                    if(count($this->sondage['choices'])<2) {
                        return "Désolé il n'y a pas suffisement de choix";
                    }

                    $this->sondage['open'] = true;
                    $res = $this->save($this->sondage);

                    return $res ? "Sondage démarré :\n".$this->getInfo() : "Impossible de démarrer un sondage";
                case "help":
                    return $this->getHelp();
                    break;
                case "stop":
                    $this->sondage['open'] = false;
                    $res = $this->save($this->sondage);
                    return "Sondage terminé.";
                    break;
                case "restore":
                    if($this->isSondageStarted())
                    {
                        return "Sondage déjà en cours :\n".$this->getInfo();
                    }else{
                        $this->sondage['open'] = true;
                        $res = $this->save($this->sondage);
                        return "Sondage réstoré :\n".$this->getInfo();
                    }

                    break;
                case "info":
                    return $this->getInfo();
                    break;
                case "votes":
                    if(count($this->sondage['votes'])>0)
                    {
                        $votes = array();
                        foreach($this->sondage['votes'] as $user=>$vote)
                        {
                            $votes[]=$user." : ".$vote;
                        }
                        return implode("\n",$votes);
                    }
                    break;
                default:
                    if($this->isSondageStarted())
                    {
                        $tmp = implode(' ',$commands);
                        if(in_array($tmp,$this->sondage['choices']))
                        {
                            if(isset($this->sondage['votes'][$user_name]))
                            {
                                return "Tu as déjà voté";
                            }else{
                                $this->sondage['votes'][$user_name]=$tmp;
                                $res = $this->save($this->sondage);
                                return $res ? "à voté" : "impossible de voter";
                            }

                        }else{
                            return $this->array_random($this->badAnswer)." Essai la commande \"info\"";
                        }
                    }else{
                        return 'Aucun sondage en cours';
                    }
                    break;
            }
        }else{
            return $this->getInfo();
        }
    }
}

return new plugin_sondage();

