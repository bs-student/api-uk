<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookDeal;
use AppBundle\Entity\BookImage;
use AppBundle\Entity\Campus;
use AppBundle\Entity\WishList;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\UniversityType;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\WishListType;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CampusType;
use AppBundle\Entity\University;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Lsw\ApiCallerBundle\Call\HttpGetJson;
use Lsw\ApiCallerBundle\Call\HttpGetHtml;
use AppBundle\Form\Type\BookType;
use Symfony\Component\HttpFoundation\FileBag;
use Gos\Bundle\WebSocketBundle\Event\PushHandlerEvent;
use Symfony\Component\Console\Output\OutputInterface;
class Subscriber implements EventSubscriber
{

    public function pushSuccess(LifecycleEventArgs $args){
        $this->index($args, 'gos_web_socket.push_success');
    }


    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array('gos_web_socket.push_success', 'gos_web_socket.push_fail');
        // TODO: Implement getSubscribedEvents() method.
    }
}
