<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
//        $data = $this->getDoctrine()->getManager()->getRepository("AppBundle:Campus")->getCampus();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $user_token= $this->get('security.token_storage')->getToken();
       /* var_dump($user->getCampus()->getState()->getCountry()->getCountryName());
        die();*/

        $json = $this->get('jms_serializer')->serialize(['user' => $user_token],'json');
        $response = new Response($json, 200);
        return $response;


//        return $this->render('default/index.html.twig',array(
//            'user' =>$user
//        ));
    }

    /**
     * @Route("/user/dashboard", name="user_homepage")
     * @Route("/user/dashboard/", name="user_homepage")
     *
     */
    public function userDashboardAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
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
            $json = $this->get('jms_serializer')->serialize(['user' => $user_data],'json');
//        $json = $this->get('jms_serializer')->serialize(['user' => $user],'json');
            $response = new Response($json, 200);
            return $response;

            /*return $this->redirect('http://localhost:8080/SymfonyClient/app/#/registration/complete?email='
                .$email."&username=".$user->getUsername()."&user=".$user->getGoogleId());*/

        }elseif($user->getRegistrationStatus()=="complete"){
            $user_data = array(
                'message' => "Login Successful",
            );
            $json = $this->get('jms_serializer')->serialize(['user' => $user_data],'json');
//        $json = $this->get('jms_serializer')->serialize(['user' => $user],'json');
            $response = new Response($json, 200);
            return $response;
//            return $this->redirect('http://localhost:8080/SymfonyClient/app/#/oauth/dashboard');
        }else{
            $user_data = array(
                'message' => "Login Successful",
            );
            $json = $this->get('jms_serializer')->serialize(['user' => $user_data],'json');
//        $json = $this->get('jms_serializer')->serialize(['user' => $user],'json');
            $response = new Response($json, 200);
            return $response;
        }

    }

    /**
     * @Route("/admin/dashboard", name="admin_homepage")
     * @Route("/admin/dashboard/", name="admin_homepage")
     *
     */
    public function adminDashboardAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('default/admin.html.twig',array(
            'user' => $user
        ));
    }
}
