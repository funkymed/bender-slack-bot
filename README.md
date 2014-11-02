#Bender Slack Bot

##Author
Cyril Pereira <cyril.pereira@gmail.com>

##Configuration
Nothing to do

##Installation
Put anywhere on a server
On your slack team area go to /services/new
Add an Outgoing WebHooks

Select a channel, trigger words must be not filled

Put a complete url with the index.php on it
ex: http://www.yourserver/bender/index.php

and that it

Go to your channel page en type !help

##Create your own plugin

create a fiile in plugins named as your want, and start with this :

~~~
class plugin_test extends Plugin
{
    protected $hook = '!trigger';

    public function getHelp()
    {
        return '!tigger';
    }

    public function getMessage($text)
    {
        $message = 'Hello world!";
        return $message;
    }
}

return new plugin_test();
~~~

the class will automatically found and executed
in your channel if you type !help you will found your !tigger