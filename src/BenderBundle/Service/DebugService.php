<?php

namespace BenderBundle\Service;

/**
 * Class DebugService
 * @package BenderBundle\Service
 */
class DebugService extends BaseService
{
    protected $hook = '!debug';

    public function getHelp()
    {
        return '!debug';
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
        $message = array();
        $message[] = '===========';
        $message[] = 'Variables :';
        $message[] = '===========';
        $data = $this->getFactory()->getData();
        foreach ($data as $k => $v) {
            $message[] = $k . " : " . $v;
        }

        $message[] = '================';
        $message[] = 'Plugins loaded : ' . count($this->getFactory()->getClasses());
        $message[] = '================';
        $classes = $this->getFactory()->getClasses();
        foreach ($classes as $k => $c) {
            $message[] = $k;
        }
        $message[] = 'Done.';

        return $message;
    }

}
