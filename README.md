Bender Slack Bot
--
Author
--
Cyril Pereira <cyril.pereira@gmail.com>

Configuration
--
```bash
$ composer install
```

Installation
--
Put anywhere on a server
On your slack team area go to /services/new
Add an Outgoing WebHooks

Select a channel, trigger words must be not filled

Put a complete url with the index.php on it
ex: http://www.yourserver/bender

and that it

Go to your channel page en type !help

Create your own plugin
--
create a service  :

```php
<?php
namespace BenderBundle\Service;

/**
 * Class YourService
 * @package BenderBundle\Service
 */
class YourService extends BaseService
{
    /**
     * @var string
     */
    protected $hook = '!yourservice';
    /**
     * @return string
     */
    public function getHelp()
    {
        return '!yourservice test';
    }
    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text)
    {
        return "ok";
    }
    protected function getAnswer($message){
        return $message;
    }
}
```

add your service to symfony in the config file and set a tag name : bender.service

```yml
    bender.allocine:
       class: BenderBundle\Service\YourService
       arguments: ["@bender.factory"]
       tags:
           -  { name: bender.services }
```

the class will automatically found and executed
in your channel if you type !help you will found your !tigger
