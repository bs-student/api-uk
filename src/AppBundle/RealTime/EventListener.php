<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 6/8/16
 * Time: 12:16 PM
 */

namespace AppBundle\RealTime;

use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Ratchet\ConnectionInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Event\PushHandlerEvent;

class EventListener {

    public function onSuccess(PushHandlerEvent $event){
        $response = $event->getResponse();
        $response->headers->setCookie(new Cookie("test", 1));
    }

    public function onFailure(PushHandlerEvent $event){
        $response = $event->getResponse();
        $response->headers->setCookie(new Cookie("test", 1));
    }

}