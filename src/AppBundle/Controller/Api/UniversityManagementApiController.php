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

class UniversityManagementApiController extends Controller
{


    /**
     * @Route("/api/university/autocomplete_activated_search_list", name="university_list")
     * @Method("POST")
     *
     */
    public function universityAutocompleteActivatedSearchListAction(Request $request)
    {


        $query = $request->request->get('query');
        $em = $this->getDoctrine()->getManager();

        if ($query == null||$query == "") {

            $json = $this->get('jms_serializer')->serialize(['university' => ""], 'json');
            $response = new Response($json, 200);
            return $response;
        }

        $universities = $em->getRepository('AppBundle:University')->getActivatedUniversitySearchResults($query);
        $data = array();
        foreach ($universities as $university) {
            array_push($data, array(
                'display' => $university['universityName'] . ", " . $university['campusName'] . ", " . $university['stateShortName'] . ", " . $university['countryName'],
                'value' => $university['campusId']
            ));

        }


        $json = $this->get('jms_serializer')->serialize($data, 'json');
        $response = new Response($json, 200);
        return $response;
    }

    /**
     * @Route("/api/university/autocomplete_university_name_search_list", name="university_name_list")
     *
     * @Method({"POST"})
     */
    public function universityAutocompleteNameSearchListAction(Request $request)
    {
        $query = $request->request->get('query');
        $em = $this->getDoctrine()->getManager();


        if ($query == null) {
            $json = $this->get('jms_serializer')->serialize(['university' => ""], 'json');
            $response = new Response($json, 200);
            return $response;
        } else {
            $universities = $em->getRepository('AppBundle:University')->getUniversitySearchResults($query);
            $json = $this->get('jms_serializer')->serialize($universities, 'json');
            $response = new Response($json, 200);
            return $response;

        }

    }

    /**
     * @Route("/api/university/search", name="university_search")
     *
     * @Method({"POST"})
     *
     */
    public function universitySearchActionAdmin(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);
        $searchQuery = $data["searchQuery"];
        $pageSize = $data["pageSize"];
        $pageNumber = $data["pageNumber"];

        $em = $this->getDoctrine()->getManager();

//        var_dump($request);
        $totalNumber = $em->getRepository('AppBundle:University')->getUniversitySearchResultNumberAdmin($searchQuery);
        $universities = $em->getRepository('AppBundle:University')->getUniversitySearchResultAdmin($searchQuery, $pageNumber, $pageSize);


        $json = $this->get('jms_serializer')->serialize(['universities' => $universities, 'totalNumber' => $totalNumber], 'json');
        $response = new Response($json, 200);
        return $response;

    }


    /**
     * Displays a form to update an Just Created User entity.
     *
     * @Route("/api/university/update_university", name="update_university")
     * @Method({"POST"})
     */
    public function updateUniversityAction(Request $request)
    {
        //Initialize Repositories
        $em = $this->getDoctrine()->getManager();
        $universityRepo = $em->getRepository('AppBundle:University');
        $serializer = $this->container->get('jms_serializer');




        //Getting Request Data
        $data = null;
        $content = $request->getContent();
        if (!empty($content)) {
            $data = json_decode($content, true);
        }

        if (count($data) > 0) {

            $message_array = array();

            foreach ($data as $request_data) {
                //Initializing Variables
                $oldUniversityUrl = "";
                $oldUniversityName = "";
                $oldUniversityStatus = "";


                if (array_key_exists("universityId", $request_data)) {


                    $university = $universityRepo->findOneBy(array(
                        'id' => $request_data['universityId']
                    ));

                    $oldUniversityName = $university->getUniversityName();
                    $oldUniversityUrl = $university->getUniversityUrl();
                    $oldUniversityStatus = $university->getUniversityStatus();

                    $university_update_form = $this->createForm(new UniversityType(), $university);
                    $university_update_form->remove('campuses');
                    $university_update_form->remove('referral');

                    $university_submitted_data = array();

                    if (array_key_exists("universityUrl", $request_data))
                        $university_submitted_data['universityUrl'] = $request_data['universityUrl'];

                    if (array_key_exists("universityName", $request_data))
                        $university_submitted_data['universityName'] = $request_data['universityName'];

                    if (array_key_exists("universityStatus", $request_data))
                        $university_submitted_data['universityStatus'] = $request_data['universityStatus'];

//                    var_dump($university_submitted_data);

                    $university_update_form->submit($university_submitted_data);


                    if ($university_update_form->isValid()) {
//
                        $em->persist($university);
                        $em->flush();
                        array_push($message_array, array(
                            'success' => "University Updated Successfully",
                            'universityId' => $request_data['universityId']
                        ));
                        $university_form_decode['children']['universityId']['value'] = "University Updated Successfully";

                    } else {
                        $em->clear();
                        $university_form = $serializer->serialize($university_update_form, 'json');
                        $university_form_decode = json_decode($university_form, true);
                        $university_form_decode['children']['universityName']['value'] = $oldUniversityName;
                        $university_form_decode['children']['universityStatus']['value'] = $oldUniversityStatus;
                        $university_form_decode['children']['universityUrl']['value'] = $oldUniversityUrl;
                        $university_form_decode['children']['universityId']['value'] = $request_data['universityId'];

                        array_push($message_array, $university_form_decode);
                    }


                }

            }

            $json = $serializer->serialize($message_array, 'json');
            $response = new Response($json, 200);
            return $response;
        } else {
            $json = $serializer->serialize(['error' => "Error on Submitting Data"], 'json');
            $response = new Response($json, 200);
            return $response;
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

        $message_array=array();
        foreach ($request_data as $university) {

            $university['universityStatus']="Activated";
            $universityEntity = new University();

            foreach($university['campuses'] as $campus){
                $campusName = null;
                $state = null;
                $campusEntity = new Campus();
                if(array_key_exists('campusName',$campus))$campusEntity->setCampusName($campus['campusName']);
                if(array_key_exists('state',$campus))$campusEntity->setState($stateRepo->findOneById($campus['state']));
                $campusEntity->setCampusStatus('Activated');     //TODO Its not working
                $universityEntity->addCampus($campusEntity);
            }

            $universityForm = $this->createForm(new UniversityType(), $universityEntity);

            $universityForm->submit($university);

            if ($universityForm->isValid()) {
                $em->persist($universityEntity);
                $em->flush();

                array_push($message_array,array(
                    'success'=>'University Successfully Created'
                ));
            } else {
                $em->clear();
                $universityFormErrorJson = $serializer->serialize($universityForm, 'json');
                array_push($message_array,json_decode($universityFormErrorJson,true));
            }

        }

        $json = $serializer->serialize($message_array, 'json');
        $response = new Response($json , 200);
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
            $message_array  = array(
                'error'=>'No University was found.'
            );

        }else{
            $em->remove($university);
            $em->flush();
            $message_array  = array(
                'success'=>'University was removed.'
            );
        }
        $json = $serializer->serialize($message_array, 'json');
        $response = new Response($json , 200);
        return $response;


    }


}
