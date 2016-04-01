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

        $form = $this->container->get('fos_user.registration.form');

        $form->remove('googleId');
        $form->remove('facebookId');
        $form->remove('googleEmail');
        $form->remove('facebookEmail');
        $form->remove('registrationStatus');
        $form->remove('registrationStatus');
        $form->remove('googleToken');
        $form->remove('facebookToken');

        $formHandler = $this->container->get('fos_user.registration.form.handler');
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




    }

    /**
     *  Register a Social Media User
     */
    public function socialRegisterAction(Request $request)
    {

        $em = $this->container->get('doctrine')->getManager();
        $userRepo = $em->getRepository('AppBundle:User');

        $requestJson = $request->getContent();
        $requestData = json_decode($requestJson, true);

        $username = array_key_exists('username', $requestData) ? $requestData['username'] : null;
        $email = array_key_exists('email', $requestData) ? $requestData['email'] : null;
        $fullName = array_key_exists('fullName', $requestData) ? $requestData['fullName'] : null;
        $googleId = array_key_exists('googleId', $requestData) ? $requestData['googleId'] : null;
        $facebookId = array_key_exists('facebookId', $requestData) ? $requestData['facebookId'] : null;
        $registrationStatus = "incomplete";
        $googleEmail = array_key_exists('googleEmail', $requestData) ? $requestData['googleEmail'] : null;
        $googleToken = array_key_exists('googleToken', $requestData) ? $requestData['googleToken'] : null;
        $facebookEmail = array_key_exists('facebookEmail', $requestData) ? $requestData['facebookEmail'] : null;
        $facebookToken = array_key_exists('facebookToken', $requestData) ? $requestData['facebookToken'] : null;

        $data = array(
            'username' => $username,
            'email' => $email,
            'fullName' => $fullName,
            'googleId' => $googleId,
            'facebookId' => $facebookId,
            'registrationStatus' => $registrationStatus,
            'googleEmail' => $googleEmail,
            'googleToken' => $googleToken,
            'facebookEmail' => $facebookEmail,
            'facebookToken' => $facebookToken
        );

        if (array_key_exists('socialService', $requestData)) {

            $serviceId = null;
            //Check if User Exist with ServiceID
            if ($requestData['socialService'] == 'google') {
                $user = $userRepo->findOneBy(array($requestData['socialService'] . "Id" => $googleId));

                $serviceId = $googleId;

            } elseif ($requestData['socialService'] == 'facebook') {
                $user = $userRepo->findOneBy(array($requestData['socialService'] . "Id" => $facebookId));

                $serviceId = $facebookId;
            }


            //If User is not Exist with ServiceId
            if (null === $user || !$user instanceof UserInterface) {

                // Check if User exist with provided email
                $user = $userRepo->findOneBy(array('email' => $email));

                //If User found with provided email
                if ($user instanceof UserInterface) {

                    //Add data which is not in the table for mering as User Exist with provided Email
                    if (($requestData['socialService'] == "google")) {
                        $user->setGoogleId($googleId);
                        $user->setGoogleToken($googleToken);
                        $user->setGoogleEmail($googleEmail);
                    }
                    if (($requestData['socialService'] == "facebook")) {
                        $user->setFacebookId($facebookId);
                        $user->setFacebookToken($facebookToken);
                        $user->setFacebookEmail($facebookEmail);
                    }
                    $em->persist($user);
                    $em->flush();

//                    //Check if user is incomplete or not
//                    if ($user->getRegistrationStatus() == "incomplete") {
//                        //Check if Email is valid or just a serviceId

                    $userData = array(
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                        'userId' => $serviceId,
                        'registrationStatus' => $user->getRegistrationStatus(),
                        'fullName' => $user->getFullName()
                    );

                    return $this->_createJsonResponse('success',array(
                        'successTitle'=>"User Successfully Updated",
                        'successDescription'=>"We had you all along and now your login data is updated.",
                        'successData'=>$userData
                    ),200);

//                    } else {
//                        return $this->_createJsonResponse('found',$serviceId);
//
//                    }


                } else {
                    $user = new User();
                    //If Email is not provided then set serviceId as Email
                    if ($email == null) {
                        $data['facebookEmail'] = $serviceId;
                        $data['email'] = $serviceId;

                    }
                    //Set Data
                    $user->addRole('ROLE_NORMAL_USER');
                    $user->setPassword('');
                    $user->setEnabled(true);
                    $user->setRegistrationStatus('incomplete');

                    //Create Form
                    $registrationForm = $this->container->get('form.factory')->create(new RegistrationType(), $user);

                    //Remove other social plugin fields
                    if ($requestData['socialService'] == "google") {
                        $registrationForm->remove('facebookId');
                        $registrationForm->remove('facebookEmail');
                        $registrationForm->remove('facebookToken');
                    }
                    if ($requestData['socialService'] == "facebook") {
                        $registrationForm->remove('googleId');
                        $registrationForm->remove('googleEmail');
                        $registrationForm->remove('googleToken');
                    }

                    //Submit & Validate form
                    $registrationForm->submit($data);
                    if ($registrationForm->isValid()) {

                        $em->persist($user);
                        $em->flush();

                        //Check if Email is valid or just a serviceId

                        $userData = array(
                            'username' => $user->getUsername(),
                            'email' => $user->getEmail(),
                            'userId' => $serviceId,
                            'registrationStatus' => $user->getRegistrationStatus(),
                            'fullName' => $user->getFullName()
                        );

                        return $this->_createJsonResponse('success',array(
                            'successTitle'=>"User Successfully Registered",
                            'successData'=>$userData
                        ),200);


                    } else {

                        return $this->_createJsonResponse('error',array(
                            'errorTitle'=>"User Registration Unsuccessful",
                            'errorDescription'=>"Form was not submitted properly. Fill Up the form and submit again.",
                            'errorData'=>$registrationForm
                        ),400);

                    }


                }

            } else {

//                //Check if found user is incomplete Then send to second page of Registration
//                if ($user->getRegistrationStatus() == "incomplete") {
//                    //Check if Email is valid or just a serviceId
                $userData = array(
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'userId' => $serviceId,
                    'registrationStatus' => $user->getRegistrationStatus(),
                    'fullName' => $user->getFullName()
                );

                return $this->_createJsonResponse('success',array(
                    'successTitle'=>"User was found in the System",
                    'successData'=>$userData
                ),200);

//                return $this->_createJsonResponse('userData', $userData,200);

//                } else {
//                    return $this->_createJsonResponse('found',$serviceId);
//                }

            }

        } else {
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"User Registration Unsuccessful",
                'errorDescription'=>"Form data was not submitted properly. Fill Up the form and submit again."
            ),400);

        }


    }

    /* public function checkIfEmailIsValid($email){
         if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
             return true;
         }else{
             return false;
         }
     }*/



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