<?php
/**
 * Created by PhpStorm.
 * User: Sujit
 * Date: 1/14/16
 * Time: 1:45 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\RegistrationType;
use Lsw\ApiCallerBundle\Call\HttpPost;
use Lsw\ApiCallerBundle\Call\HttpPostJson;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Lsw\ApiCallerBundle\Call\HttpGetJson;
use Lsw\ApiCallerBundle\Call\HttpGetHtml;
use GuzzleHttp;


class RegistrationController extends BaseController
{

    /**
     * Check  if Username is Exist in the Symtem
     */
    public function checkIfUsernameExistAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);
        $searchQuery = $data["query"];

        $em = $this->container->get('doctrine')->getManager();
        $usernameExist = $em->getRepository('AppBundle:User')->checkIfNewUsernameExist($searchQuery);

        return $this->_createJsonResponse('success',array('usernameExist' => $usernameExist),200);


    }

    /**
     * Check  if Email is Exist in the Symtem
     */
    public function checkIfEmailExistAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);
        $searchQuery = $data["query"];

        $em = $this->container->get('doctrine')->getManager();
        $emailExist = $em->getRepository('AppBundle:User')->checkIfNewEmailExist($searchQuery);

        return $this->_createJsonResponse('success',array('emailExist' => $emailExist),200);


    }

    /**
     * Registers a Normal User
     */
    public function registerAction()
    {
        $formHandler = $this->container->get('fos_user.registration.form.handler');
        $submittedData = $formHandler->getSubmittedData();


        if(array_key_exists('key',$submittedData)){

            $captchaApiInfo = $this->container->getParameter('google_re_captcha_info');

            $host = $captchaApiInfo['host'];
            $secret = $captchaApiInfo['secret'];

            $url= $host."?secret=".$secret."&response=".$submittedData['key'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
            $jsonOutput = curl_exec($ch);
            curl_close($ch);

//            if(curl_errno($ch))
//            {
//                echo 'Curl error: ' . curl_error($ch);
//            }
//            var_dump(curl_getinfo($ch));
//            curl_close($ch);
//
//            var_dump($jsonOutput);
//            die();
//            $jsonOutput = $this->container->get('api_caller')->call(new HttpGetJson($url,array(),false,array()));

            $captchaResponse = json_decode($jsonOutput,true);



            if($captchaResponse['success']){
                $form = $this->container->get('fos_user.registration.form');

                $form->remove('googleId');
                $form->remove('facebookId');
                $form->remove('googleEmail');
                $form->remove('facebookEmail');
                $form->remove('registrationStatus');
                $form->remove('registrationStatus');
                $form->remove('googleToken');
                $form->remove('facebookToken');



                $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');


                $process = $formHandler->process($confirmationEnabled);

                if ($process) {
                    $user = $form->getData();

                    $authUser = false;
                    if ($confirmationEnabled) {
                        $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                        $route = 'fos_user_registration_check_email';

                    } else {
                        $authUser = true;
                        $route = 'fos_user_registration_confirmed';
                    }

                    $message = array(
                        'successTitle' => "Registration Successful",
                        'successDescription' => "A verification Email has been sent to your mail. Please check verify your email to confirm registration."
                    );

                    $this->setFlash('fos_user_success', 'registration.flash.user_created');
                    $url = $this->container->get('router')->generate($route);
                    $response = new RedirectResponse($url);

                    if ($authUser) {
                        $this->authenticateUser($user, $response);
                    }

                    return $this->_createJsonResponse('success',$message,201);


                }

                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"User Registration Unsuccessful",
                    'errorDescription'=>"Sorry we were unable to register you. Reload the page and try again.",
                    'errorData'=>$form
                ),400);
            }else{
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"User Registration Unsuccessful",
                    'errorDescription'=>"Captcha was Wrong. Reload and try again."
                ),400);
            }

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"User Registration Unsuccessful",
                'errorDescription'=>"Sorry we were unable to register you. FillUp the form and try again."
            ),400);
        }







    }

    /**
     * Tell the user to check his email provider
     *
     */
    public function checkEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $this->container->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Registration:checkEmail.html.' . $this->getEngine(), array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);
        $serializer = $this->container->get('jms_serializer');
        if (null === $user) {
            return $this->confirmationTokenExpiredAction();
//            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setLastLogin(new \DateTime());

        $this->container->get('fos_user.user_manager')->updateUser($user);
        $response = new RedirectResponse($this->container->get('router')->generate('fos_user_registration_confirmed'));
        $this->authenticateUser($user, $response);

        $data = array(
            'successTitle' => "Registration Confirmed",
            "successDescription" => "The Account has been Confirmed"
        );
        return $this->_createJsonResponse('success', $data,200);

    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $serializer = $this->container->get('jms_serializer');
        if (!is_object($user) || !$user instanceof UserInterface) {

            $data = array(
                'errorTitle' => "Access Denied",
                "errorDescription" => "This user does not have access to this section"
            );
            return $this->_createJsonResponse('error', $data,400);

        } else {
            $data = array(
                'successTitle' => "Registration Confirmed",
                "successDescription" => "The Account has been Confirmed"
            );
            return $this->_createJsonResponse('success', $data, 200);

        }

    }


    /**
     * Tell the user his account is now confirmed
     */
    public function confirmationTokenExpiredAction()
    {
        $data = array(
            'errorTitle' => "Confirmation Failed",
            "errorDescription" => "Sorry, The Confirmation token has been expired"
        );
        return $this->_createJsonResponse('error', $data, 400);

    }


    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }
} 