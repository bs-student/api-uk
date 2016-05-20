<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 */

namespace AppBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the password change
 *
 */
class ChangePasswordController extends BaseController
{

    /**
     * Change password
     */
    public function changePasswordAction()
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $data = array(
                'errorTitle'=>"Access Denied",
                "errorDescription"=>"Sorry, This user does not have access to this section."
            );
            return $this->_createJsonResponse('error',$data,400);

        }

        $form = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {

            $data = array(
                'successTitle'=>"Password Changed",
                "successDescription"=>"Password is successfully changed."
            );
            return $this->_createJsonResponse('success',$data,200);

        }else{

            $data = array(
                'errorTitle'=>"Sorry, Password could not be changed"
            );
            return $this->_createJsonResponse('error',$data,400);
        }

    }

    public function _createJsonResponse($key,$data,$code){
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
