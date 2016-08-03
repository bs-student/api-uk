<?php

namespace AppBundle\Controller\Api;

use AppBundle\Validator\Constraints\UsernameConstraints;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\UserType;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper;
use Symfony\Component\Validator\ConstraintViolation;


class AdminUserApiController extends Controller
{

    /**
     * Get all Non Approved users
     *
     */
    public function getAllNonApprovedUserAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $userRepo=$em->getRepository('AppBundle:User');


            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $emailQuery = filter_var($data["emailQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];



            $totalNumber = $userRepo->getNonApprovedUserSearchNumber($searchQuery,$emailQuery);
            $users = $userRepo->getNonApprovedUserSearchResult($searchQuery,$emailQuery, $pageNumber, $pageSize,$sort);

            $data = array(
                'totalUsers' => $users ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('users'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }


    }

    /**
     * Get all Approved users
     *
     */
    public function getAllApprovedUserAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $userRepo=$em->getRepository('AppBundle:User');


            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $emailQuery = filter_var($data["emailQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];



            $totalNumber = $userRepo->getApprovedUserSearchNumber($searchQuery,$emailQuery);
            $users = $userRepo->getApprovedUserSearchResult($searchQuery,$emailQuery, $pageNumber, $pageSize,$sort);


            $data = array(
                'totalUsers' => $users ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('users'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }


    }

    /**
     * Get all Admin Users
     *
     */
    public function getAllAdminUserAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $userRepo=$em->getRepository('AppBundle:User');


            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $emailQuery = filter_var($data["emailQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];


            $totalNumber = $userRepo->getAdminUserSearchNumber($searchQuery,$emailQuery);
            $users = $userRepo->getAdminUserSearchResult($searchQuery,$emailQuery, $pageNumber, $pageSize,$sort);

            $data = array(
                'totalUsers' => $users ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('users'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }

    }

    /**
     * Admin Update User Data
     */
    public function adminUpdateUserDataAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');

        $request_data = json_decode($request->getContent(),true);


        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){
            $editedUser = $userRepo->findOneBy(array("id" => $request_data['userId']));

            if ($editedUser != null) {

                if ($userRepo->checkIfUsernameExistByUsername($request_data['username'], $editedUser->getUsername())) {

                    return $this->_createJsonResponse('error',array(
                        'errorTitle'=>"Can't Approve User",
                        'errorDescription'=> "Username '" . $request_data['username'] . "' Already Exist",
                        'errorData'=> array(
                            'username'=> $user->getusername()
                        )
                    ),400);

                } else {

                    $editedUser->setUserName($request_data['username']);
                    $editedUser->setEnabled($request_data['enabled']);
                    $editedUser->setAdminApproved('Yes');
                    $em->persist($editedUser);
                    $em->flush();

                    return $this->_createJsonResponse('success',array(
                        'successTitle'=>"User Approved",
                    ),200);

                }

            }
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }


    }

    /**
     * Approve Users
     */
    public function approveUsersAction(Request $request){
        $content = $request->getContent();
        $data = json_decode($content, true);
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');


        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            if(count($data)>0){
                $data = $userRepo->approveUsers($data);

                return $this->_createJsonResponse('success',array(
                    'successTitle'=>"Users been Approved",
                ),200);
            }else{
                return $this->_createJsonResponse('error', array('errorTitle'=>"No User was approved."), 400);
            }


        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }


    }

    /**
     * Add Admin Users
     */
    public function addAdminUserAction(Request $request)
    {
//        $content = $request->getContent();
//        $data = json_decode($content, true);
//        var_dump($data);
//        die();
        $user = $this->container->get('security.context')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $formHandler = $this->container->get('fos_user.registration.form.handler');

            $form = $this->container->get('fos_user.registration.form');

            $form->remove('googleId');
            $form->remove('facebookId');
            $form->remove('googleEmail');
            $form->remove('facebookEmail');
            $form->remove('registrationStatus');
            $form->remove('registrationStatus');
            $form->remove('googleToken');
            $form->remove('facebookToken');
            $form->remove('campus');
            $form->remove('referral');
            $form->remove('googleToken');


            $confirmationEnabled = false;


            $process = $formHandler->process($confirmationEnabled);

            if ($process) {

                $em = $this->getDoctrine()->getManager();

                $addedUser = $form->getData();
                $addedUser->addRole("ROLE_ADMIN_USER");

                $em->persist($addedUser);
                $em->flush();

                $addedUserData=array(
                    'email'=>$addedUser->getEmail(),
                    'enabled'=>$addedUser->isEnabled(),
                    'fullName'=>$addedUser->getFullName(),
                    'roles'=>$addedUser->getRoles(),
                    'userId'=>$addedUser->getId(),
                    'username'=>$addedUser->getUsername(),
                    'profilePicture'=>$addedUser->getProfilePicture()
                );

                return $this->_createJsonResponse('success', array(
                    'successTitle'=>"Admin User Added",
                    'successData'=>$addedUserData
                ), 201);


            }else{
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => "Admin User Couldn't be created",
                    'errorDescription' => "Sorry we were unable to add admin user. Reload the page and try again.",
                    'errorData' => $form
                ), 400);
            }


        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }

    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }
}
