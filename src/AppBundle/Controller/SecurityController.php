<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 1/15/16
 * Time: 6:46 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Log;
use AppBundle\Form\Type\LogType;
use AppBundle\Form\Type\UserType;
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
        if(!strcmp($error,"User account is disabled.")){
            $error.=" Please Check Your Email for the Activation Link.";
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

            $logData = array(
                'user'=>$user->getId(),
                'logType'=>"Login",
                'logDateTime'=>gmdate('Y-m-d H:i:s'),
                'logDescription'=> $user->getUsername()." has Logged In",
                'userIpAddress'=>$this->container->get('request')->getClientIp(),
                'logUserType'=> in_array("ROLE_ADMIN_USER",$user->getRoles())?"Admin User":"Normal User"
            );
            $this->_saveLog($logData);

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


    public function _saveLog($logData){
        $em = $this->container->get('doctrine')->getManager();
        $log = new Log();
        $logForm = $this->container->get('form.factory')->create(new LogType(), $log);

        $logForm->submit($logData);
        if($logForm->isValid()){
            $em->persist($log);
            $em->flush();
        }
    }

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }


} 