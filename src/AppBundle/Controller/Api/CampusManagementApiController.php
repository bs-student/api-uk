<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Campus;
use AppBundle\Form\Type\UniversityType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CampusType;
use AppBundle\Entity\University;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;

class CampusManagementApiController extends Controller
{


    /**
     * @Route("/api/campus/list", name="campus_list_by_university")
     *
     * @Method({"POST"})
     *
     */
    public function campusListByUniversityAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);
        $universityId = $data["universityId"];

        $em = $this->getDoctrine()->getManager();
        $campusRepo = $em->getRepository('AppBundle:Campus');

        $campuses = $campusRepo->getCampusesByUniversityId($universityId);

        return $this->createJsonResponse('campuses',$campuses);


    }


    /**
     * Displays a form to update an Just Created User entity.
     *
     * @Route("/api/campus/update", name="update_campus")
     * @Method({"POST"})
     */
    public function updateUniversityAction(Request $request)
    {
        //Initialize Repositories
        $em = $this->getDoctrine()->getManager();
        $campusRepo = $em->getRepository('AppBundle:Campus');
        $serializer = $this->container->get('jms_serializer');


        //Getting Request Data
        $data = null;
        $content = $request->getContent();
        if (!empty($content)) {
            $data = json_decode($content, true);
        }
        if (count($data) > 0) {
            if (array_key_exists("id", $data)) {


                $campus = $campusRepo->findOneBy(array(
                    'id' => $data['id']
                ));

                $oldCampusName = $campus->getCampusName();
                $oldCampusStatus = $campus->getCampusStatus();
                $oldCampusId = $campus->getId();


                $campus_update_form = $this->createForm(new CampusType(), $campus);
                $campus_update_form->remove('state');

                $campus_update_form->submit($data);


                if ($campus_update_form->isValid()) {

                    $em->persist($campus);
                    $em->flush();

                    $array = array(
                        'successTitle' => "Campus Updated Successfully",
                        'successBody'=>"Campus has been updated. please check the list for update result."
                    );
                    return $this->createJsonResponse('success',$array);

                } else {

                    $array = array(
                        'errorTitle' => "Campus Could not be Updated",
                        'errorBody'=>"Sorry there is a problem with the form data. Please check and submit again.",
                        'campusStatus'=>$oldCampusStatus,
                        'campusName'=>$oldCampusName,
                        'campusId'=>$oldCampusId
                    );
                    return $this->createJsonResponse('error',$array);

                }


            }else{
                return $this->createJsonResponse('error',array('errorTitle'=>"Error on submitting Data",'errorBody'=>"Please check the fields and try again."));
            }

        } else {
            return $this->createJsonResponse('error',array('errorTitle'=>"Error on submitting Data",'errorBody'=>"Please check the fields and try again."));
        }


    }

    /**
     * Save new Universities.
     *
     * @Route("/api/university/save_new_university", name="save_new_university")
     * @Method({"POST"})
     */
    public function saveNewUniversityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $serializer = $this->container->get('jms_serializer');
        $stateRepo = $em->getRepository("AppBundle:State");
        $request_data = json_decode($request->getContent(), true);

        $message_array = array();
        foreach ($request_data as $university) {

            $university['universityStatus'] = "Activated";
            $universityEntity = new University();

            foreach ($university['campuses'] as $campus) {
                $campusName = null;
                $state = null;
                $campusEntity = new Campus();
                if (array_key_exists('campusName', $campus)) $campusEntity->setCampusName($campus['campusName']);
                if (array_key_exists('state', $campus)) $campusEntity->setState($stateRepo->findOneById($campus['state']));

                $universityEntity->addCampus($campusEntity);
            }

            $universityForm = $this->createForm(new UniversityType(), $universityEntity);

            $universityForm->submit($university);

            if ($universityForm->isValid()) {
                $em->persist($universityEntity);
                $em->flush();

                array_push($message_array, array(
                    'success' => 'University Successfully Created'
                ));
            } else {
                $em->clear();
                $universityFormErrorJson = $serializer->serialize($universityForm, 'json');
                array_push($message_array, json_decode($universityFormErrorJson, true));
            }

        }

        $json = $serializer->serialize($message_array, 'json');
        $response = new Response($json, 200);
        return $response;


    }

    /**
     * Displays a form to update an Just Created User entity.
     *
     * @Route("/api/university/delete", name="delete_university")
     * @Method({"POST"})
     */
    public function deleteUniversityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $serializer = $this->container->get('jms_serializer');
        $universityRepo = $em->getRepository("AppBundle:University");
        $request_data = json_decode($request->getContent(), true);

        $university = $universityRepo->findOneById($request_data['deleteId']);

        $message_array = null;
        if (!$university) {
            $message_array = array(
                'error' => 'No University was found.'
            );

        } else {
            $em->remove($university);
            $em->flush();
            $message_array = array(
                'success' => 'University was removed.'
            );
        }
        $json = $serializer->serialize($message_array, 'json');
        $response = new Response($json, 200);
        return $response;


    }

    public function createJsonResponse($key,$data){
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, 200);
        return $response;
    }

}
