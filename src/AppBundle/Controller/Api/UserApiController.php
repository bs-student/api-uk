<?php

namespace AppBundle\Controller\Api;

use AppBundle\Form\Type\ProfileType;
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



    /*public function updateUserAction(Request $request)
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
    }*/

    /*private function getErrorMessages(\Symfony\Component\Form\Form $form)
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
    }*/


    /**
     * Get Current user Short Details
     */
    public function currentUserShortDetailsAction()
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        if ($user) {
            $user_data = array(
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName(),
                'email' => $user->getEmail(),
                'registrationStatus' => $user->getRegistrationStatus(),
                'userId' => ($user->getGoogleId() != null) ? $user->getGoogleId() : $user->getFacebookId(),
                'campusId' => $user->getCampus()?$user->getCampus()->getId():'',
                'standardHomePhone' => $user->getStandardHomePhone(),
                'standardCellPhone' => $user->getStandardCellPhone(),
                'standardEmail' => $user->getStandardEmail(),
                'role'=>$user->getRoles()
            );

            return $this->_createJsonResponse('success',array(
                'successData'=>$user_data,
            ),200);

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"User was not identified",
            ),400);

        }
    }

    /**
     * Get Current user Full Details
     */
    public function currentUserFullDetailsAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user) {
            $user_data = array(
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'fullName' => $user->getFullName(),
                'email' => $user->getEmail(),
                'registrationStatus' => $user->getRegistrationStatus(),
                'userId' => ($user->getGoogleId() != null) ? $user->getGoogleId() : $user->getFacebookId(),
                'campusName' => $user->getCampus()->getCampusName(),
                'campusId' => $user->getCampus()->getId(),
                'universityName' => $user->getCampus()->getUniversity()->getUniversityName(),
                'stateName' => $user->getCampus()->getState()->getStateName(),
                'stateShortName' => $user->getCampus()->getState()->getStateShortName(),
                'countryName' => $user->getCampus()->getState()->getCountry()->getCountryName(),
                'standardHomePhone' => $user->getStandardHomePhone(),
                'standardCellPhone' => $user->getStandardCellPhone(),
                'standardEmail' => $user->getStandardEmail(),
                'role'=>$user->getRoles()
            );

            return $this->_createJsonResponse('success',array(
                'successData'=>$user_data,
            ),200);

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>"User was not identified",
            ),400);
        }

    }


    /**
     * All Users List Admin
     *
     */
    public function adminAllUsersAction()
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

        return $this->_createJsonResponse('success',array(
            'successData'=>$users,
        ),200);


    }




    /**
     * Update User Full Name
     */
/*    public function updateUserFullNameDataAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $serializer = $this->container->get('jms_serializer');

        $request_data = json_decode($request->getContent(), true);
//        $user = $userRepo->findOneBy(array("id" => $request_data['id']));

        if ($user != null) {
//            $oldFullName = $user->getFullName();
            $updateForm = $this->createForm(new UserType(), $user);
            $updateForm->remove('username');
            $updateForm->remove('email');
            $updateForm->remove('referral');
            $updateForm->remove('campus');
            $updateForm->remove('wishLists');

            $updateForm->submit($request_data);


            if ($updateForm->isValid()) {
                $em->persist($user);
                $em->flush();
                return $this->_createJsonResponse('success', array('successTitle' => 'Full Name is Updated', 'successDescription' => 'Your full name is successfully updated.'),200);
            } else {
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => 'Full Name is not Updated',
                    'errorDescription' => 'Sorry. Please check the form and submit again.',
                    'errorData'=>$updateForm
                ),400);
            }
        }
    }*/


    /**
     * Update User University Campus
     */
/*    public function updateUserUniversityCampusAction(Request $request)
    {
//        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('AppBundle:User');
        $serializer = $this->container->get('jms_serializer');

        $request_data = json_decode($request->getContent(), true);

        $user = $userRepo->findOneBy(array("id" => $request_data['id']));

        if ($user != null) {

            $oldUniversityName= $user->getCampus()->getUniversity()->getUniversityName();
            $oldCampusName = $user->getCampus()->getCampusName();
            $oldStateShortName = $user->getCampus()->getState()->getStateShortName();
            $oldCountryName = $user->getCampus()->getState()->getCountry()->getCountryname();

            $updateForm = $this->createForm(new UserType(), $user);
            $updateForm->remove('fullName');
            $updateForm->remove('username');
            $updateForm->remove('email');
            $updateForm->remove('referral');


            $updateForm->submit($request_data);


            if ($updateForm->isValid()) {
                $em->persist($user);
                $em->flush();
                return $this->_createJsonResponse('success', array(
                    'successTitle' => 'University is Successfully Changed',
                    'successDescription' => 'Your university is successfully changed.',
                    'successData'=>array(
                        'universityName'=>$user->getCampus()->getUniversity()->getUniversityName(),
                        'campusName'=>$user->getCampus()->getCampusName(),
                        'stateShortName' =>$user->getCampus()->getState()->getStateShortName(),
                        'countryName'=>$user->getCampus()->getState()->getCountry()->getCountryname()
                    )

                ),200);
            } else {

                return $this->_createJsonResponse('error', array(
                    'errorTitle' => 'University was not changed.',
                    'errorDescription' => 'Sorry. Please check the form and submit again.',
                    'errorData'=>array(
                        'form' => $serializer->serialize($updateForm, 'json'),
                        'universityName'=>$oldUniversityName,
                        'campusName'=>$oldCampusName,
                        'stateShortName' =>$oldStateShortName,
                        'countryName'=>$oldCountryName
                    )

                ),400);
            }
        }
    }*/

    /**
     * Update User Profile
     */
    public function updateUserProfileAction(Request $request){

        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
//        $userRepo = $em->getRepository('AppBundle:User');
//        $serializer = $this->container->get('jms_serializer');

        $data = json_decode($request->getContent(), true);
//        $user = $userRepo->findOneBy(array("id" => $request_data['id']));

        if ($user != null) {
//            $oldFullName = $user->getFullName();
            $updateForm = $this->createForm(new ProfileType(), $user);


            $updateForm->submit($data);


            if ($updateForm->isValid()) {
                $em->persist($user);
                $em->flush();

                $userData=array(
                    'campusId'=>$user->getCampus()->getId(),
                    'campusName'=>$user->getCampus()->getCampusName(),
                    'countryName'=>$user->getCampus()->getState()->getCountry()->getCountryName(),
                    'stateName'=>$user->getCampus()->getState()->getStateName(),
                    'stateShortName'=>$user->getCampus()->getState()->getStateShortName(),
                    'universityName'=>$user->getCampus()->getUniversity()->getUniversityName()
                );

                return $this->_createJsonResponse('success', array('successTitle' => 'Profile is Updated','successData'=>$userData),200);
            } else {
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => 'Full Name is not Updated',
                    'errorDescription' => 'Sorry. Please check the form and submit again.',
                    'errorData'=>$updateForm
                ),400);
            }
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
