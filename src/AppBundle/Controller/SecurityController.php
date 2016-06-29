<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 1/15/16
 * Time: 6:46 PM
 */

namespace AppBundle\Controller;

use AppBundle\Form\Type\UserType;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;


class SecurityController extends BaseController {

    /**
     *  Show Homepage
     */
    public function indexAction()
    {

        return $this->_createJsonResponse('success',array(
            "successTitle" => "Homepage",
            "successDescription"=> "You have successfully accessed the Web Api"
        ),200);
    }

    /**
     *  Show Login Page & Show Errors too
     *
     */
    public function loginAction()
    {
        $request = $this->container->get('request');

        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $session = $request->getSession();
        /* @var $session \Symfony\Component\HttpFoundation\Session\Session */

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }

        return $this->_createJsonResponse('error',array("errorTitle" => "Login Unsuccessful", "errorDescription" => $error),400);
    }

    /**
     *  Show if User log in successfully or not
     *
     */
    public function userDashboardAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if($user->getRegistrationStatus()=="incomplete"){
            if(filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                //Mail Exists
                $email="false";
            }else{
                //Mail NOT Exists
                $email="true";
            }
            $userId=null;
            if($user->getGoogleId()!=null){
                $userId =$user->getGoogleId();
            }
            if($user->getFacebookId()!=null){
                $userId =$user->getFacebookId();
            }

            $user_data = array(
                'username'=>$user->getUsername(),
                'email_needed'=>$email,
                'userId'=>$userId

            );

            return $this->_createJsonResponse('success',array(
                'successTitle' => "Login Successful",
                'successDescription'=>"Please Complete your registration process.",
                'successData'=>$user_data
            ),200);



            /*return $this->redirect('http://localhost:8080/SymfonyClient/app/#/registration/complete?email='
                .$email."&username=".$user->getUsername()."&user=".$user->getGoogleId());*/

        }elseif($user->getRegistrationStatus()=="complete"){

            return $this->_createJsonResponse('success',array(
                'successTitle' => "Login Successful"
            ),200);

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle' => "Login Unsuccessful",
                'errorDescription' => "Please try to Login again."
            ),400);
        }

    }

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

//    function _create_custom_token($data) {
//
//        $now_seconds = time();
//        $payload = array(
//            "iss" => $data['client_email'],
//            "sub" => $data['client_email'],
//            "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
//            "iat" => $now_seconds,
//            "exp" => $now_seconds+(60*60),  // Maximum expiration time is one hour
//            "uid" => $data['client_id'],
//        );
//        return JWT::encode($payload, $data['private_key'], "HS256");
//    }

} 