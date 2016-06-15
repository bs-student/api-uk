<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 6/8/16
 * Time: 12:16 PM
 */

namespace AppBundle\Live;

use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Gos\Bundle\WebSocketBundle\Topic\PushableTopicInterface;
use Ratchet\ConnectionInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\Wamp\Topic;

class LiveNotification implements TopicInterface, PusherInterface,PushableTopicInterface{

    protected $clientManipulator;

    /**
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function __construct(ClientManipulatorInterface $clientManipulator)
    {
        $this->clientManipulator = $clientManipulator;
    }


    /**
     * @param  ConnectionInterface $connection
     * @param  Topic $topic
     * @param WampRequest $request
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
//        var_dump("Helooooooooooooooooooooooooo");
//        var_dump($this->clientManipulator->findByUsername($topic,'sujit'));
//        var_dump("Hello");
//        var_dump($topic);


//        $connection->send(\GuzzleHttp\json_encode("YOu are subscribed to all/user"));
        // TODO: Implement onSubscribe() method.
    }

    /**
     * @param  ConnectionInterface $connection
     * @param  Topic $topic
     * @param WampRequest $request
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        // TODO: Implement onUnSubscribe() method.
    }

    /**
     * @param  ConnectionInterface $connection
     * @param  Topic $topic
     * @param WampRequest $request
     * @param $event
     * @param  array $exclude
     * @param  array $eligible
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        // TODO: Implement onPublish() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gos_web_socket.topic';
    }

//    public function send()
//    {
////        var_dump($this->clientManipulator->getAll());
//        return 'gos_web_socket.topic';
//    }

    /**
     * @param string|array $data
     * @param string $routeName
     * @param array[] $routeParameters
     */
    public function push($data, $routeName, Array $routeParameters = array(), Array $context = [])
    {
        var_dump("HI");
        // TODO: Implement push() method.
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        // TODO: Implement getConfig() method.
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        // TODO: Implement setConfig() method.
    }

    public function close()
    {
        // TODO: Implement close() method.
    }

    /**
     * @param Topic $topic
     * @param WampRequest $request
     * @param string|array $data
     * @param string $provider
     */
    public function onPush(Topic $topic, WampRequest $request, $data, $provider)
    {
        var_dump("HIIII");
        // TODO: Implement onPush() method.
    }
}