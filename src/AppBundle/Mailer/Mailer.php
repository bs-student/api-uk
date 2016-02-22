<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Mailer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Mailer\Mailer as BaseClass;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Mailer extends BaseClass
{


    public function sendConfirmationEmailMessage(UserInterface $user)
    {

//        var_dump($user->getConfirmationToken());
//        die();
        $template = $this->parameters['confirmation.template'];
//        $url = $this->router->generate('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);
        $url = "http://localhost:8080/Student2StudentApp/app/#/security/confirm/".$user->getConfirmationToken();
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'confirmationUrl' =>  $url
        ));
//        var_dump($this->parameters);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['confirmation'], $user->getEmail());
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {

        $template = $this->parameters['resetting.template'];
//        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);
        $url = "http://localhost:8080/Student2StudentApp/app/#/security/reset/".$user->getConfirmationToken();
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'confirmationUrl' => $url
        ));
//        var_dump($this->parameters);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $user->getEmail());
    }


}
