<?php
/**
 * Class plugin_gif
 */

class plugin_gif extends Plugin
{
    protected $hook = '!gif';

    public function getHelp()
    {
        return '!gif';
    }

    public function getMessage($text)
    {
        $commands   = $this->getCommands($text);
        $query      = urlencode(implode(' ',$commands));
        $res        = $this->get_page_content('http://api.giphy.com/v1/gifs/search?q='.$query.'&api_key=dc6zaTOxFJmzC');
        if($res)
        {
            $json = json_decode($res);
            shuffle($json->data);
            return $json->data[0]->images->fixed_height_downsampled->url;
        }

    }

}

return new plugin_gif();