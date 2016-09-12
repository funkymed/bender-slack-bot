<?php

namespace BenderBundle\Service;

/**
 * Class AllocineMovie
 * @package BenderBundle\Service
 */
class AllocineMovie
{
    public $name;
    public $poster;
    public $url;
    public $numberOfTheatre;

    public function __construct($name, $poster, $url, $numberOfTheatre)
    {
        $this->name = $name;
        $this->poster = $poster;
        $this->url = $url;
        $this->numberOfTheatre = $numberOfTheatre;
    }

    static function sortByPopularity(AllocineMovie $a, AllocineMovie $b)
    {
        $diff = $a->numberOfTheatre - $b->numberOfTheatre;

        return ($diff < -1 ? +1 : ($diff > 1 ? -1 : $diff));
    }
}