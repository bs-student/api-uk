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


class UserApiController extends Controller
{

    /**
     * Displays a form to update an Just Created User entity.
     *
     * @Route("/api/user/update_created_profile", name="update_created_profile")
     * @Method({"POST"})
     */
    public function updateCreatedProfileAction(Request $request)
    {
        //Initialize Error Variables

        $username_exist = false;
        $username_exist_message = "Username Already Exist";
        $email_exist = false;
        $email_exist_message = "Email Already Exist";


        //Initialize Repositories
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $referralRepo = $em->getRepository('AppBundle:Referral');
        $campusRepo = $em->getRepository('AppBundle:Campus');
        $serializer = $this->container->get('jms_serializer');

        //Get DATA from request
        $data = null;
        $content = $request->getContent();

            $data = json_decode($content, true); // 2nd param to get as array


        //Pull User by Service ID
        if ($userRepo->findOneBy(array("googleId" => $data['serviceId']))) {
            $user = $userRepo->findOneBy(array("googleId" => $data['serviceId']));
        } else {
            $user = $userRepo->findOneBy(array("facebookId" => $data['serviceId']));
        }

        //Create & Modify Form as needed
//        $user->setReferral($referralRepo->findOneBy(array('id'=>$data['referral'])));
//        $user->setCampus($referralRepo->findOneBy(array('id'=>$data['campus'])));
        $update_form = $this->createForm(new UserType(), $user);

        if (!array_key_exists("email", $data)) {
            $update_form->remove('email');
        }
        if (!array_key_exists("fullName", $data)) {
            $update_form->remove('fullName');
        }

        //Check if Username Already Exist
        if ($userRepo->checkIfUsernameExistByUsername($data['username'], $user->getUsername())) {
            $username_exist = true;
            $update_form->get('username')->addError(new FormError('Username Already Exist.'));
        }

        //Submit Data
        $submitted_data = array(
            'username'=>$data['username'],
            'referral'=>$data['referral'],
            'campus'=>$data['campus']
        );

        //Add additional Data as needed

        if (array_key_exists("email", $data)) {
            //Check if Email Exists or Not
            $submitted_data['email'] = $data['email'];
            if ($userRepo->checkIfEmailExistByEmail($data['email'], $user->getEmail())) {
                $email_exist = true;
                $update_form->get('email')->addError(new FormError('Email Already Exists.'));
            }
        }

        if (array_key_exists("fullName", $data)) {
            $submitted_data['fullName'] = $data['fullName'];
        }

        $update_form->submit($submitted_data);


//        print("--------FINDING EXTRA FIELDS--------------");
//        $debug_children = $update_form->all();
//
//        print("<br/>FORM CHILDREN<br/>");
//        foreach ($debug_children as $ch) {
//            print($ch->getName() . "<br/>");
//        }
//
//        $debug_data = array_diff_key($data, $debug_children);
//        var_dump($debug_data);
//        //$debug_data contains now extra fields
//
//        print("<br/>DIFF DATA<br/>");
//        foreach ($debug_data as $k => $d) {
//            print("$k: <pre>");
//            print_r($d);
//            print("</pre>");
//        }
//        print("---------------------------------------------");


        //Add messages if Username or Email Exist
        $form = $serializer->serialize($update_form, 'json');
        $tempArray = json_decode($form, true);

        if ($email_exist) {

            if(empty($tempArray['children']['email'])){
                $tempArray['children']['email']['errors']= array($email_exist_message);
            }else{
                array_push($tempArray['children']['email']['errors'], $email_exist_message);
            }
        }
        if ($username_exist) {
            if(empty($tempArray['children']['username'])){
                $tempArray['children']['username']['errors']= array($username_exist_message);
            }else{
                array_push($tempArray['children']['username']['errors'], $username_exist_message);
            }

        }

        //Form Validity Check
        if ($update_form->isValid()) {

            if (!$email_exist && !$username_exist) {
                $user->setRegistrationStatus("complete");
                $em->persist($user);
                $em->flush();
                $json = $serializer->serialize(['success' => "User is successfully updated"], 'json');
                $response = new Response($json, 200);
                return $response;
            }else{

                $response = new Response(json_encode($tempArray), 200);
                return $response;
            }

        } else {

            $response = new Response(json_encode($tempArray), 200);
            return $response;

        }

    }


    /**
     * Displays a form to update User entity.
     *
     * @Route("/user/update_profile", name="update_profile")
     * @Method({"GET", "POST"})
     */
    public function updateUserAction(Request $request)
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($user->getRegistrationStatus() == "incomplete") {
            return $this->redirectToRoute('update_created_profile', array(), 301);
        }

        $editForm = $this->createForm(new UserType(), $user);

        $editForm->remove('email');
        $editForm->remove('username');
        $editForm->remove('referral');

        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();


            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user_homepage', array(), 301);


//            return $this->redirectToRoute('post_edit', array('id' => $post->getId()));
        }

        return $this->render('user/update.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),

        ));
    }

    private function getErrorMessages(\Symfony\Component\Form\Form $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }

            $errors[$key] = $template;
        }
        if ($form->count()) {
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child);
                }
            }
        }
        return $errors;
    }


    /**
     * @Route("/api/current_user_short_details", name="current_user_short_details")
     */
    public function currentUserShortDetailsAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user) {
            $user_data = array(
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName(),
                'email' => $user->getEmail(),
                'registrationStatus'=>$user->getRegistrationStatus(),
                'userId'=> ($user->getGoogleId()!=null)?$user->getGoogleId():$user->getFacebookId(),
            );
            $json = $this->get('jms_serializer')->serialize(['user' => $user_data], 'json');
            $response = new Response($json, 200);
            return $response;
        }

        $json = $this->get('jms_serializer')->serialize(['error' => "User is not identified"], 'json');
        $response = new Response($json, 400);
        return $response;

    }

    /**
     * @Route("/api/current_user_full_details", name="current_user_full_details")
     */
    public function currentUserFullDetailsAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user) {
            $user_data = array(
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName(),
                'email' => $user->getEmail(),
                'registrationStatus'=>$user->getRegistrationStatus(),
                'userId'=> ($user->getGoogleId()!=null)?$user->getGoogleId():$user->getFacebookId(),
                'campusName' => $user->getCampus()->getCampusName(),
                'universityName' => $user->getCampus()->getUniversity()->getUniversityName(),
                'stateName' => $user->getCampus()->getState()->getStateName(),
                'stateShortName' => $user->getCampus()->getState()->getStateShortName(),
                'countryName' => $user->getCampus()->getState()->getCountry()->getCountryName()
            );
            $json = $this->get('jms_serializer')->serialize(['user' => $user_data], 'json');
            $response = new Response($json, 200);
            return $response;
        }

        $json = $this->get('jms_serializer')->serialize(['error' => "User is not identified"], 'json');
        $response = new Response($json, 400);
        return $response;

    }


    /**
     * @Route("/api/admin/all_users", name="admin_all_users")
     *
     */
    public function adminAllUsers()
    {
//        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $users = $userRepo->findAllUsers();


//        if ($user) {
//            $user_data = array(
//                'username' => $user->getUsername(),
//                'fullName' => $user->getFullName(),
//                'email' => $user->getEmail(),
//                'campus' => $user->getCampus()->getCampusName(),
//                'university' => $user->getCampus()->getUniversity()->getUniversityName(),
//                'state' => $user->getCampus()->getState()->getStateName(),
//                'state_short_name' => $user->getCampus()->getState()->getStateShortName(),
//                'country' => $user->getCampus()->getState()->getCountry()->getCountryName()
//            );
//            $json = $this->get('jms_serializer')->serialize(['user' => $user_data], 'json');
//            $response = new Response($json, 200);
//            return $response;
//        }

        $json = $this->get('jms_serializer')->serialize(['users' => $users], 'json');
        $response = new Response($json, 200);
        return $response;

    }


    /**
     * @Route("/api/admin/update_user_data", name="admin_update_users")
     * @Method({"POST"})
     */
    public function adminUpdateUserData(Request $request)
    {
//        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');

        $request_data = json_decode($request->getContent());
        $user = $userRepo->findOneBy(array("id" => $request_data->id));

        if ($user != null) {

            if ($userRepo->checkIfUsernameExistByUsername($request_data->username, $user->getUsername())) {
                $json = $this->get('jms_serializer')->serialize(['error' => "Username '" . $request_data->username . "' Already Exist", 'username' => $user->getusername()], 'json');
            } else {

                $user->setUserName($request_data->username);
                $em->persist($user);
                $em->flush();
                $json = $this->get('jms_serializer')->serialize(['success' => "User Updated"], 'json');
            }
            $response = new Response($json, 200);
            return $response;
        }
    }
}
