<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ChangePasswordController extends BaseController
{

    /**
     * Change user password
     * @Route("/profile/change-password", name="fos_user_change_password")
     * @Method({"POST","GET"})
     */
    public function changePasswordAction()
    {

        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            $data = array(
                'errorTitle'=>"Access Denied",
                "errorBody"=>"Sorry, This user does not have access to this section."
            );
            return $this->createJsonResponse('error',$data);

        }

        $form = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {

            $data = array(
                'successTitle'=>"Password Changed",
                "successBody"=>"Password is successfully changed."
            );
            return $this->createJsonResponse('success',$data);

        }else{

            $data = array(
                'errorTitle'=>"Password could not be changed",
                "errorBody"=>"Sorry, please check the form nad try again.."
            );
            return $this->createJsonResponse('error',$data);
        }

    }

    public function createJsonResponse($key,$data){
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, 200);
        return $response;
    }

}
