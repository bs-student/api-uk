<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 6/8/16
 * Time: 12:16 PM
 */

namespace AppBundle\RealTime;

use Gos\Bundle\WebSocketBundle\Event\PushHandlerEvent;
use Symfony\Component\HttpFoundation\Response;

class EventListener  {

    public function onSuccess(PushHandlerEvent $event){

        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        $txt = $event->getMessage();
        fwrite($myfile, $txt);
        fclose($myfile);
        return new Response('<body>Hello</body>');
    }

    public function onFailure(PushHandlerEvent $event){
        $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
        $txt = $event->getMessage();
        fwrite($myfile, $txt);
        fclose($myfile);
        return new Response('<body>Hello</body>');
    }

}