<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\State;
use AppBundle\Form\StateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
/**
 * State controller.
 *
 */
class StateManagementApiController extends Controller
{

    /**
     * @Route("/api/state/list_by_country", name="all_states_by_country")
     *
     * @Method({"POST"})
     *
     */
    public function indexAction(Request $request)
    {

        $request_data = json_decode($request->getContent());

        $em = $this->getDoctrine()->getManager();


        $states = $em->getRepository('AppBundle:State')->findBy(array(
            'country'=> $request_data->countryId
        ));

        $json = $this->get('jms_serializer')->serialize($states, 'json');
        $response = new Response($json, 200);
        return $response;
    }

}
