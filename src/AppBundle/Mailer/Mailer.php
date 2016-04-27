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
        $url = "http://168.61.173.224:8080/Student2Student/#/confirmRegistration/".$user->getConfirmationToken();
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'confirmationUrl' =>  $url
        ));
//        var_dump($this->parameters);
        $this->sendEmailMessage($rendered,$this->parameters['from_email']['confirmation'], $user->getEmail());
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {

        $template = $this->parameters['resetting.template'];
//        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);
        $url = "http://168.61.173.224:8080/Student2Student/#/resetPassword/".$user->getConfirmationToken();
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'confirmationUrl' => $url
        ));
//        var_dump($this->parameters);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $user->getEmail());
    }


    public function operateContactMailingProcess($bookDeal,$book,$seller,$buyerInfo,$buyerMessage)
    {

        if(!strcmp($bookDeal->getBookContactMethod(),"buyerToSeller")){
            $toSellerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_seller_mail.html.twig", array(
                'bookDeal' => $bookDeal,
                'seller' => $seller,
                'buyerInfo'=>$buyerInfo,
                'book'=>$book,
                'buyerMessage'=>$buyerMessage
            ));
            $this->sendEmailMessage($toSellerRendered, $this->parameters['from_email']['resetting'], $seller->getEmail());

            $toBuyerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_buyer_mail.html.twig", array(
                'bookDeal' => $bookDeal,
                'seller' => $seller,
                'buyerInfo'=>$buyerInfo,
                'book'=>$book,
                'buyerMessage'=>$buyerMessage
            ));
            $this->sendEmailMessage($toBuyerRendered, $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
        }else{
            $toSellerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_seller_mail.html.twig", array(
                'bookDeal' => $bookDeal,
                'seller' => $seller,
                'buyerInfo'=>$buyerInfo,
                'book'=>$book,
                'buyerMessage'=>$buyerMessage
            ));

            $this->sendEmailMessage($toSellerRendered, $this->parameters['from_email']['resetting'], $seller->getEmail());

            $toBuyerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_buyer_mail.html.twig", array(
                'bookDeal' => $bookDeal,
                'seller' => $seller,
                'buyerInfo'=>$buyerInfo,
                'book'=>$book,
                'buyerMessage'=>$buyerMessage
            ));
            $this->sendEmailMessage($toBuyerRendered, $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
        }




    }

    public function operateMessageMailingProcess($contact,$message,$messageType){
        if($contact->getBuyer()!=null){
            $buyer = $contact->getBuyer()->getUsername();
        }else{
            $buyer = $contact->getBuyerNickName();
        }
        $data=array(
            'contact'=>$contact,
            'bookDeal' => $contact->getBookDeal(),
            'seller' => $contact->getBookDeal()->getSeller(),
            'buyer'=>$buyer,
            'book'=>$contact->getBookDeal()->getBook(),
            'message'=>$message
        );

        if(!strcmp($messageType,"buyerSendingToSeller")){
            $this->_buyerToSellerMessageMail($data);
        }elseif(!strcmp($messageType,"sellerSendingToBuyer")){
            $this->_sellerToBuyerMessageMail($data);
        }


    }

    public function _buyerToSellerMessageMail($data){

//        var_dump("Sender: ". $data['buyer']->getUsername());
//        var_dump("Receiver: ". $data['seller']->getUsername());

        $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_seller.html.twig", $data);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $data['bookDeal']->getBookContactEmail());

        $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_buyer.html.twig", $data);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $data['contact']->getBuyerEmail());


    }
    public function _sellerToBuyerMessageMail($data){
//        var_dump("Sender: ". $data['seller']->getUsername());
//        var_dump("Receiver: ". $data['buyer']->getUsername());

        $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_buyer.html.twig", $data);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $data['contact']->getBuyerEmail());

        $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_seller.html.twig", $data);
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $data['bookDeal']->getBookContactEmail());

    }
    /**
     * @param string $renderedTemplate
     * @param string $toEmail
     */
    function sendEmailMessage($renderedTemplate,$fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body,'text/html');

        $this->mailer->send($message);
    }

}
