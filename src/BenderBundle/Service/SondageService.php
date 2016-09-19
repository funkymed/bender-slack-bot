<?php

namespace BenderBundle\Service;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class SondageService
 * @package BenderBundle\Service
 */
class SondageService extends BaseService
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

    public function getKeyCache(){
        $team_domain = $this->getTeamDomain();
        return 'sondage_'.$team_domain;
    }

    public function getHelp()
    {
        return '!sondage start question/choix1,choix2,...|stop|restore|info|votes';
    }

    /**
     * @return mixed
     */
    public function getSondage()
    {
        $data = $this->cache->fetch($this->getKeyCache());
        return $data ? $data : [];
    }

    /**
     * @param $sondage
     * @return bool
     */
    public function save($sondage)
    {
        $res = $this->cache->save($this->getKeyCache(),$sondage);
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

            return floor($count/$total*100);
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
        $answer = $this->checkAnswer($text);
        return $answer ? $this->getAnswer($answer) : "";
    }

    /**
     * @param $text
     * @return array|string
     */
    public function checkAnswer($text)
    {
        $user_name = $this->getUserName();

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

    protected function getAnswer($message){
        $sondage = $this->sondage;
        if(!$this->isSondageStarted()){
            return parent::getAnswer($message);
        }

        $choices=[];
        foreach($sondage['choices'] as $choice){
            $choices[]=[
                "name" => $choice,
                "text" => $choice,
                "value" => $choice,
                "type" => "button",
            ];
        }

        $date = new \DateTime();
        return [
            "attachments"=>[
                [
                    "title"=>"Sondage",
                    "color"=> "#FF5555",
                    "footer"=> "Bender",
                    "callback_id"=> "!sondage",
                    "footer_icon"=>$this->getContainer()->getParameter('url_bender')."/bundles/bender/icons/sncf.png",
                    "title_link"=> "http://www.sncf.com",
                    "text"=>$sondage['question'],
                    "actions"=>$choices,
                    "attachment_type"=>"default",
                    "ts"=> $date->format('U')
                ]
            ]
        ];
    }
}
