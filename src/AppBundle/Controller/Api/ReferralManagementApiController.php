<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Referral;
use AppBundle\Form\ReferralType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Referral controller.
 *
 */
class ReferralManagementApiController extends Controller
{

    /**
     * @Route("/api/referral/list", name="all_referrals")
     *
     *
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $referrals = $em->getRepository('AppBundle:Referral')->findAll();

        $json = $this->get('jms_serializer')->serialize($referrals, 'json');
        $response = new Response($json, 200);
        return $response;
    }

}
