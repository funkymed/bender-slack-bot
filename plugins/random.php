<?php

/**
 * Class plugin_random
 */
class plugin_random extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!random';

    /**
     * @param $text
     * @return string
     */
    public function getMessage($text) {
        $list1  = include "data/01.php";
        $list2  = include "data/02.php";
        $list3  = include "data/03.php";
        $list4  = include "data/04.php";
        $phrase = array(
          $this->array_random($list1),
          $this->array_random($list2),
          $this->array_random($list3),
          $this->array_random($list4)
        );

        return implode(' ',$phrase);
    }
}

return new plugin_random();