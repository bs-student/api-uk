<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Country;
use AppBundle\Form\CountryType;

/**
 * Country controller.
 *
 */
class CountryManagementApiController extends Controller
{

    /**
     * @Route("/api/country/list", name="all_countries")
     *
     *
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $countries = $em->getRepository('AppBundle:Country')->findAllCountry();

        $json = $this->get('jms_serializer')->serialize($countries, 'json');
        $response = new Response($json, 200);
        return $response;
    }

}
