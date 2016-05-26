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
use AppBundle\Form\Type\SocialRegistrationType;
use AppBundle\Form\Type\UserType;
use Lsw\ApiCallerBundle\Call\HttpPost;
use Lsw\ApiCallerBundle\Call\HttpPostJson;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Lsw\ApiCallerBundle\Call\HttpGetJson;
use Lsw\ApiCallerBundle\Call\HttpGetHtml;
use GuzzleHttp;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SocialRegistrationController extends Controller
{


    /**
     * Check for Google User
     */
    public function authGoogleAction(Request $request)
    {
        $requestJson = $request->getContent();
        $requestData = json_decode($requestJson, true);

        $client = new GuzzleHttp\Client();

        $params = [
            'code' => $requestData['code'],
            'client_id' => $requestData['clientId'],
            'client_secret' => $this->container->getParameter('google_app_info')['client_secret'],
            'redirect_uri' => $requestData['redirectUri'],
            'grant_type' => 'authorization_code',
        ];


        // Step 1. Exchange authorization code for access token.

        $accessTokenResponse = $client->request('POST', 'https://accounts.google.com/o/oauth2/token', [
            'form_params' => $params
        ]);
        $accessToken = json_decode($accessTokenResponse->getBody(), true);


        // Step 2. Retrieve profile information about the current user.
        $profileResponse = $client->request('GET', 'https://www.googleapis.com/plus/v1/people/me/openIdConnect', [
            'headers' => array('Authorization' => 'Bearer ' . $accessToken['access_token'])
        ]);
        $profile = json_decode($profileResponse->getBody(), true);



        // Step 3a. If user is already signed in then link accounts.

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $user = $userRepo->findOneBy(array('email'=>$profile['email']));

        //Check if user found
        if($user instanceof User){

            //If User doesn't have Google Data
            if($user->getGoogleId()==null){

                //Update Data & Login

                $userForm = $this->createForm(new SocialRegistrationType(), $user);
                $userForm->remove('fullName');
                $userForm->remove('username');
                $userForm->remove('email');
                $userForm->remove('adminApproved');
                $userForm->remove('registrationStatus');
                $userForm->remove('referral');
                $userForm->remove('campus');
                $userForm->remove('facebookId');
                $userForm->remove('facebookEmail');
                $userForm->remove('facebookToken');

                $data=array(
                    'googleId' =>$profile['sub'],
                    'googleEmail' =>$profile['email'],
                    'googleToken' => $accessToken['access_token'],
                );
                $userForm->submit($data);

                if ($userForm->isValid()) {
                    $em->persist($user);
                    $em->flush();
                    return $this->_createJsonResponse('success',array(
                            'successTitle'=>"You account has been merged with Google Account.",
                            'successData'=>array(
                                'username'=>$user->getUsername(),
                                'fullName'=>$user->getFullName(),
                                'email'=>$user->getEmail(),
                                'registrationStatus'=>$user->getRegistrationStatus(),
                                'serviceId'=>$user->getGoogleId(),
                                'service'=>'google'
                            ))
                        ,200);
                }else{
                    return $this->_createJsonResponse('error',array(
                            'errorTitle'=>"Sorry couldn't merge your data to existed user with mail ".$profile['email'],
                            'errorDescription'=>"Please Try Again Later",
                            'errorData'=>$userForm)
                        ,400);
                }
            }else{
                // Google Data is merged so Return Data to Login
                return $this->_createJsonResponse('success',array(
                        'successData'=>array(
                            'username'=>$user->getUsername(),
                            'fullName'=>$user->getFullName(),
                            'email'=>$user->getEmail(),
                            'registrationStatus'=>$user->getRegistrationStatus(),
                            'serviceId'=>$user->getGoogleId(),
                            'service'=>'google'
                        ))
                    ,200);
            }




        }else{
            //Register
            $userEntity = new User();

            $userEntity->addRole('ROLE_NORMAL_USER');
            $userEntity->setPassword('');
            $userEntity->setEnabled(true);

            $userForm = $this->createForm(new SocialRegistrationType(), $userEntity);
            $userForm->remove('referral');
            $userForm->remove('campus');
            $userForm->remove('facebookId');
            $userForm->remove('facebookEmail');
            $userForm->remove('facebookToken');

            $data=array(
                'email'=>  $profile['email'],
                'username'=>  $profile['given_name'].$profile['family_name'].intval(rand(1,9999999999)),
                'fullName' => $profile['name'],
                'googleId' =>$profile['sub'],
                'googleEmail' =>$profile['email'],
                'googleToken' => $accessToken['access_token'],
                'adminApproved' =>"No",
                'registrationStatus'=>"incomplete",

            );

            $userForm->submit($data);

            if ($userForm->isValid()) {
                $em->persist($userEntity);
                $em->flush();
                return $this->_createJsonResponse('success',array(
                        'successTitle'=>"You have been registered.",
                        'successDescription'=>"Please Fill Up the Next form to complete registration Process",
                        'successData'=>array(
                            'username'=>$userEntity->getUsername(),
                            'fullName'=>$userEntity->getFullName(),
                            'email'=>$userEntity->getEmail(),
                            'registrationStatus'=>$userEntity->getRegistrationStatus(),
                            'serviceId'=>$userEntity->getGoogleId(),
                            'service'=>'google'
                        ))
                    ,200);
            }else{
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Sorry we couldn't register you",
                    'errorDescription'=>"Please Try Again Later",
                    'errorData'=>$userForm)
                    ,400);
            }

        }


    }

    /**
     * Check for Facebook User
     */
    public function authFacebookAction(Request $request)
    {
        $requestJson = $request->getContent();
        $requestData = json_decode($requestJson, true);

        $client = new GuzzleHttp\Client();
        $params = [
            'code' => $requestData['code'],
            'client_id' => $requestData['clientId'],
            'client_secret' => $this->container->getParameter('facebook_app_info')['client_secret'],
            'redirect_uri' => $requestData['redirectUri']
        ];

        // Step 1. Exchange authorization code for access token.

        $accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.5/oauth/access_token', [
            'query' => $params
        ]);
        $accessToken = json_decode($accessTokenResponse->getBody(), true);

        // Step 2. Retrieve profile information about the current user.

        $fields = 'id,email,first_name,last_name,link,name';
        $profileResponse = $client->request('GET', 'https://graph.facebook.com/v2.5/me', [
            'query' => [
                'access_token' => $accessToken['access_token'],
                'fields' => $fields
            ]
        ]);
        $profile = json_decode($profileResponse->getBody(), true);


        // Step 3a. If user is already signed in then link accounts.
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        if(array_key_exists('email',$profile)){
            $user = $userRepo->findOneBy(array('email'=>$profile['email']));
            $email = $profile['email'];
            $emailNeeded = false;
        }else{
            $user = $userRepo->findOneBy(array('facebookId'=>$profile['id']));
            $email = $profile['id']."@facebook.com";
            $emailNeeded = true;
        }


        //Check if user found
        if($user instanceof User){

            //If User doesn't have Google Data
            if($user->getFacebookId()==null){

                //Update Data & Login

                $userForm = $this->createForm(new SocialRegistrationType(), $user);
                $userForm->remove('fullName');
                $userForm->remove('username');
                $userForm->remove('email');
                $userForm->remove('adminApproved');
                $userForm->remove('registrationStatus');
                $userForm->remove('referral');
                $userForm->remove('campus');
                $userForm->remove('googleId');
                $userForm->remove('googleEmail');
                $userForm->remove('googleToken');

                $data=array(
                    'facebookId' =>$profile['id'],
                    'facebookEmail' =>$email,
                    'facebookToken' => $accessToken['access_token'],
                );
                $userForm->submit($data);

                if ($userForm->isValid()) {
                    $em->persist($user);
                    $em->flush();
                    return $this->_createJsonResponse('success',array(
                            'successTitle'=>"You account has been merged with Facebook Account.",
                            'successData'=>array(
                                'username'=>$user->getUsername(),
                                'fullName'=>$user->getFullName(),
                                'email'=>$user->getEmail(),
                                'registrationStatus'=>$user->getRegistrationStatus(),
                                'serviceId'=>$user->getFacebookId(),
                                'emailNeeded'=>$emailNeeded,
                                'service'=>'facebook'
                            ))
                        ,200);
                }else{
                    return $this->_createJsonResponse('error',array(
                            'errorTitle'=>"Sorry couldn't merge your data to existed user with mail ".$profile['email'],
                            'errorDescription'=>"Please Try Again Later",
                            'errorData'=>$userForm)
                        ,400);
                }
            }else{
                // Google Data is merged so Return Data to Login
                return $this->_createJsonResponse('success',array(
                        'successData'=>array(
                            'username'=>$user->getUsername(),
                            'fullName'=>$user->getFullName(),
                            'email'=>$user->getEmail(),
                            'registrationStatus'=>$user->getRegistrationStatus(),
                            'serviceId'=>$user->getFacebookId(),
                            'emailNeeded'=>$emailNeeded,
                            'service'=>'facebook'
                        ))
                    ,200);
            }




        }else{
            //Register
            $userEntity = new User();

            $userEntity->addRole('ROLE_NORMAL_USER');
            $userEntity->setPassword('');
            $userEntity->setEnabled(true);

            $userForm = $this->createForm(new SocialRegistrationType(), $userEntity);
            $userForm->remove('referral');
            $userForm->remove('campus');
            $userForm->remove('googleId');
            $userForm->remove('googleEmail');
            $userForm->remove('googleToken');

            $data=array(
                'email'=>  $email,
                'username'=>  $profile['first_name'].$profile['last_name'].intval(rand(1,9999999999)),
                'fullName' => $profile['name'],
                'facebookId' =>$profile['id'],
                'facebookEmail' =>$email,
                'facebookToken' => $accessToken['access_token'],
                'adminApproved' =>"No",
                'registrationStatus'=>"incomplete",

            );

            $userForm->submit($data);

            if ($userForm->isValid()) {
                $em->persist($userEntity);
                $em->flush();
                return $this->_createJsonResponse('success',array(
                        'successTitle'=>"You have been registered.",
                        'successDescription'=>"Please Fill Up the Next form to complete registration Process",
                        'successData'=>array(
                            'username'=>$userEntity->getUsername(),
                            'fullName'=>$userEntity->getFullName(),
                            'email'=>$userEntity->getEmail(),
                            'registrationStatus'=>$userEntity->getRegistrationStatus(),
                            'serviceId'=>$userEntity->getFacebookId(),
                            'emailNeeded'=>$emailNeeded,
                            'service'=>'facebook'
                        ))
                    ,200);
            }else{
                return $this->_createJsonResponse('error',array(
                        'errorTitle'=>"Sorry we couldn't register you",
                        'errorDescription'=>"Please Try Again Later",
                        'errorData'=>$userForm)
                    ,400);
            }

        }
    }

    /**
     * Update Social User.
     *
     */
    public function updateSocialUserAction(Request $request)
    {
        $requestJson = $request->getContent();
        $requestData = json_decode($requestJson, true);

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');

        $service = $requestData['user']['service'];
        $user = $userRepo->findOneBy(array($service."Id"=>$requestData['user']['serviceId']));

        if($user instanceof User){

            if($userRepo->checkIfUsernameExistByUsername($requestData['user']['username'], $user->getUsername())){
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Username Already Exist",
                    'errorDescription'=>"Please provide different username"
                ),400);
            }

            if($userRepo->checkIfEmailExistByEmail($requestData['user']['email'], $user->getEmail())){
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>"Email Already Exist",
                    'errorDescription'=>"Please provide different email"
                ),400);
            }

            $userForm = $this->createForm(new SocialRegistrationType(), $user);
            $userForm->remove('fullName');
            $userForm->remove('adminApproved');
            $userForm->remove('googleId');
            $userForm->remove('googleEmail');
            $userForm->remove('googleToken');
            $userForm->remove('facebookId');
            $userForm->remove('facebookEmail');
            $userForm->remove('facebookToken');

            $data=array(
                'registrationStatus'=>"complete",
                'referral'=>$requestData['user']['referral'],
                'campus'=>$requestData['user']['campus'],
                'username'=>$requestData['user']['username'],
                'email'=>$requestData['user']['email']
            );

            $userForm->submit($data);
            if($userForm->isValid()){
                $em->persist($user);
                $em->flush();
                return $this->_createJsonResponse('success',array(
                        'successTitle'=>"Your Registration is Completed",
                        'successData'=>array(
                            'username'=>$user->getUsername(),
                            'fullName'=>$user->getFullName(),
                            'email'=>$user->getEmail(),
                            'registrationStatus'=>$user->getRegistrationStatus(),
                            'serviceId'=>$requestData['user']['service']=='google'?$user->getGoogleId():$user->getFacebookId(),
                            'service'=>$requestData['user']['service']
                        ))
                    ,200);
            }else{
                return $this->_createJsonResponse('error',array(
                        'errorTitle'=>"Sorry registration couldn't be completed",
                        'errorDescription'=>"Please Try Again Later",
                        'errorData'=>$userForm)
                    ,400);
            }

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"Sorry User was not found"
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
} 