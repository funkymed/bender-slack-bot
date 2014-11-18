<?php 
/**
 * Class plugin_recipe
 */

class plugin_recipe extends Plugin
{
    protected $hook = '!recette';

    public function getHelp()
    {
        return '!recette';
    }

    private function get_page_content($url) {
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      curl_setopt($ch, CURLOPT_URL, $url);

      $content = curl_exec($ch);
      curl_close($ch);

      return $content;
    }

    public function getMessage($text)
    {
        $content_page_redirect = explode('</html>',$this->get_page_content("http://www.marmiton.org/recettes/recette-hasard.aspx"));

        $doc = new DOMDocument();
        $doc->loadHTML($content_page_redirect[0].'</html>', LIBXML_NOWARNING);

        $links = $doc->getElementsByTagName('a');

        foreach ($links as $link) {
            $link_recipe = urldecode("http://www.marmiton.org".$link->getAttribute('href'));
        }

        $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $content_page = file_get_contents($link_recipe,false,$context);

        $doc = new DOMDocument();

        $previous_value = libxml_use_internal_errors(TRUE);
        $doc->loadHTML((string)$content_page);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_value);

        $links = $doc->getElementsByTagName('h1');

        foreach ($links as $link) {
           $recette = $link->nodeValue;
        }

        return $recette." : ".$link_recipe;
    }

}

return new plugin_recipe();