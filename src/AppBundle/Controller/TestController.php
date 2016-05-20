<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 */

namespace AppBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;


/**
 * Controller managing the password change
 *
 */
class TestController extends Controller
{


    /**
     * Change password
     */
    public function indexAction()
    {

        $message = \Swift_Message::newInstance();


        $data = array(
            'user' => "User",
            'confirmationUrl' =>  "url",
            'header_image'=>$message->embed(\Swift_Image::fromPath('assets/images/header.png')),
            'banner_image'=>$message->embed(\Swift_Image::fromPath('assets/images/banner.png')),
            'sub_title_image'=>$message->embed(\Swift_Image::fromPath('assets/images/subTitle.png')),
            'button_activate_account_image'=>$message->embed(\Swift_Image::fromPath('assets/images/activate_button.png')),
            'button_tell_friends_image'=>$message->embed(\Swift_Image::fromPath('assets/images/tell_friend_button.png')),
            'share_image'=>$message->embed(\Swift_Image::fromPath('assets/images/share.png')),
            'footer_image'=>$message->embed(\Swift_Image::fromPath('assets/images/footer.png')),
        );




       /* $message->setBody(
            $this->templating->render('mail_templates/registration_confirmation.html.twig', $data),'text/html'
        );*/


//        $image= $message->embed(\Swift_Image::fromPath('http://localhost:8080/Student2StudentApi/web/assets/images/logo.jpg'));
//        var_dump($message->embed(\Swift_Image::fromPath('http://localhost:8080/Student2StudentApi/web/assets/images/logo.jpg')));

//        return $this->render('mail_templates/registration_confirmation.html.twig',array('image'=>$image));
//        die();

        return $this->render('mail_templates/registration_confirmation.html.twig',$data);

    }

    public function _createJsonResponse($key,$data,$code){
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
