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


class ContactUsApiController extends Controller
{


    /**
     * Send Contact Message
     */
    public function sendMessageAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $this->get('fos_user.mailer')->sendContactUsEmail($data);

        return $this->_createJsonResponse('success',array(
            'successTitle'=>"Your message has been sent",
            'successDescription'=>"Authority will contact you as soon as possible"
        ),201);

    }
    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }
}
