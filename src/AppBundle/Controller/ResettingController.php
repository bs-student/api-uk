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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Controller\ResettingController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Controller managing the resetting of the password
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends BaseController
{




    /**
     * Request reset user password: submit form and send email
     * @Route("/resetting/send-email", name="fos_user_resetting_send_email")
     * @Method("POST")
     */


    public function sendEmailAction()
    {

        $username = $this->container->get('request')->request->get('username');

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            $data = array(
                'errorTitle'=>"Cannot Reset Password",
                "errorBody"=>"Sorry No User found on that email Address"
            );
            return $this->createJsonResponse('error',$data);
        }

        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            $data = array(
                'errorTitle'=>"Cannot Reset Password",
                "errorBody"=>"Sorry the Reset Password was already requested"
            );
            return $this->createJsonResponse('error',$data);

        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);

        $data = array(
            'successTitle'=>"Reset Password Successful",
            "successBody"=>"A mail has been sent to your email Address for resetting Password"
        );
        return $this->createJsonResponse('success',$data);
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $session = $this->container->get('session');
        $email = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:checkEmail.html.'.$this->getEngine(), array(
            'email' => $email,
        ));
    }

    /**
     * Reset user password
     * @Route("/resetting/reset/{token}", name="fos_user_resetting_reset")
     * @Method({"POST","GET"})
     */
    public function resetAction($token=null)
    {

        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            $data = array(
                'errorTitle'=>"Cannot Reset Password",
                "errorBody"=>"The user with 'confirmation token' does not exist for value '$token'"
            );
            return $this->createJsonResponse('error',$data);
//            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        if (!$user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_resetting_request'));
        }

        $form = $this->container->get('fos_user.resetting.form');
        $formHandler = $this->container->get('fos_user.resetting.form.handler');

        $process = $formHandler->process($user);

        if ($process) {
            $this->setFlash('fos_user_success', 'resetting.flash.success');
            $response = new RedirectResponse($this->getRedirectionUrl($user));
            $this->authenticateUser($user, $response);

            $data = array(
                'successTitle'=>"Reset Password Successful",
                "successBody"=>"Password has been successfully changed"
            );
            return $this->createJsonResponse('success',$data);

        }else{
            $data = array(
                'errorTitle'=>"Cannot Reset Password",
                "errorBody"=>"Sorry, the password could not be changed"
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
