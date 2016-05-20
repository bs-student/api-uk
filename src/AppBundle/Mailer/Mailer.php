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
        $message = \Swift_Message::newInstance();
        $url = "http://168.61.173.224:8080/Student2Student/#/confirmRegistration/".$user->getConfirmationToken();
        $data = array(
            'user' => $user->getUsername(),
            'confirmationUrl' =>  $url,
            'header_image'=>$message->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
            'button_activate_account_image'=>$message->embed(\Swift_Image::fromPath('assets/images/activate_button.jpg')),
            'button_tell_friends_image'=>$message->embed(\Swift_Image::fromPath('assets/images/tell_friend_button.jpg')),
            'share_image'=>$message->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
            'footer_image'=>$message->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
        );

        $message->setBody(
            $this->templating->render('mail_templates/registration_confirmation.html.twig',$data),'text/html'
        );

        $this->_sendMail($message,"Student2Student.com : Confirm Registration Process",$this->parameters['from_email']['confirmation'],$user->getEmail());
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {

        $message = \Swift_Message::newInstance();
        $url = "http://168.61.173.224:8080/Student2Student/#/resetPassword/".$user->getConfirmationToken();
        $data = array(
            'user' => $user->getUsername(),
            'confirmationUrl' =>  $url,
            'header_image'=>$message->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
            'button_reset_password_image'=>$message->embed(\Swift_Image::fromPath('assets/images/reset.jpg')),
            'button_tell_friends_image'=>$message->embed(\Swift_Image::fromPath('assets/images/tell_friend_button.jpg')),
            'share_image'=>$message->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
            'footer_image'=>$message->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
        );

        $message->setBody(
            $this->templating->render('mail_templates/reset_mail.html.twig',$data),'text/html'
        );

        $this->_sendMail($message,"Student2Student.com : Reset Password",$this->parameters['from_email']['resetting'],$user->getEmail());

    }


    public function operateContactMailingProcess($bookDeal,$book,$seller,$buyerInfo,$buyerMessage)
    {


        if(!strcmp($bookDeal->getBookContactMethod(),"buyerToSeller")){

            //Sending First mail
            $message1 = \Swift_Message::newInstance();

            $toSellerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_seller_mail.html.twig", array(
                'bookDeal' => $bookDeal,
                'seller' => $seller,
                'buyerInfo'=>$buyerInfo,
                'book'=>$book,
                'buyerMessage'=>$buyerMessage,
                'book_image'=>$message1->embed(\Swift_Image::fromPath(substr($book->getBookImage(),1))),
                'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                'share_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
                'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
            ));

            $message1->setBody($toSellerRendered,'text/html');
            $this->_sendMail($message1,"Student2Student: Buyer Contacting You", $this->parameters['from_email']['resetting'], $seller->getEmail());

            //Sending 2nd Mail
            $message2 = \Swift_Message::newInstance();
            $toBuyerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_buyer_mail.html.twig", array(
                'bookDeal' => $bookDeal,
                'seller' => $seller,
                'buyerInfo'=>$buyerInfo,
                'book'=>$book,
                'buyerMessage'=>$buyerMessage,
                'book_image'=>$message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(),1))),
                'header_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                'share_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
                'footer_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
            ));
            $message2->setBody($toBuyerRendered,'text/html');
            $this->_sendMail($message2, "Student2Student: Seller Contact Information ", $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
        }else{
            //Sending First Mail
            $message1 = \Swift_Message::newInstance();

            $toSellerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_seller_mail.html.twig", array(
                'bookDeal' => $bookDeal,
                'seller' => $seller,
                'buyerInfo'=>$buyerInfo,
                'book'=>$book,
                'buyerMessage'=>$buyerMessage,
                'book_image'=>$message1->embed(\Swift_Image::fromPath(substr($book->getBookImage(),1))),
                'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                'share_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
                'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
            ));


            $message1->setBody($toSellerRendered,'text/html');

            $this->_sendMail($message1,"Student2Student: Buyer Contact Information", $this->parameters['from_email']['resetting'], $seller->getEmail());

            //Sending 2nd Mail
            $message2 = \Swift_Message::newInstance();

            $toBuyerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_buyer_mail.html.twig", array(
                'bookDeal' => $bookDeal,
                'seller' => $seller,
                'buyerInfo'=>$buyerInfo,
                'book'=>$book,
                'buyerMessage'=>$buyerMessage,
                'book_image'=>$message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(),1))),
                'header_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                'share_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
                'footer_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
            ));
            $message2->setBody($toBuyerRendered,'text/html');
            $this->_sendMail($message2,"Student2Student: Sent Information to Seller", $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
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
        $this->sendEmailMessage($rendered,"Message from Buyer", $this->parameters['from_email']['resetting'], $data['bookDeal']->getBookContactEmail());

        $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_buyer.html.twig", $data);
        $this->sendEmailMessage($rendered, "Message sent to Seller",$this->parameters['from_email']['resetting'], $data['contact']->getBuyerEmail());


    }
    public function _sellerToBuyerMessageMail($data){
//        var_dump("Sender: ". $data['seller']->getUsername());
//        var_dump("Receiver: ". $data['buyer']->getUsername());

        $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_buyer.html.twig", $data);
        $this->sendEmailMessage($rendered, "Message from Seller" ,$this->parameters['from_email']['resetting'], $data['contact']->getBuyerEmail());

        $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_seller.html.twig", $data);
        $this->sendEmailMessage($rendered, "Message sent to Buyer", $this->parameters['from_email']['resetting'], $data['bookDeal']->getBookContactEmail());

    }




    /**
     * @param string $renderedTemplate
     * @param string $toEmail
     */
    function sendEmailMessage($renderedTemplate,$fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
//        $renderedLines = explode("\n", trim($renderedTemplate));
//        $subject = $renderedLines[0];
//        $body = implode("\n", array_slice($renderedLines, 1));
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername('sujit.developer.136663@gmail.com')
            ->setPassword('maniac.sujit');
        $mailer = \Swift_Mailer::newInstance($transport);

        var_dump($toEmail);
        die();
        $message = \Swift_Message::newInstance()
            ->setSubject("SUBJECT")
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody('<a href="#">sdrgd</a>','text/html');

        $mailer->send($message);
    }

//    public function sendEmail($template,$subject,$from,$to){
//        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
//            ->setUsername('sujit.developer.136663@gmail.com')
//            ->setPassword('maniac.sujit');
//        $mailer = \Swift_Mailer::newInstance($transport);
//        $message = \Swift_Message::newInstance()
//            ->setSubject($subject)
//            ->setFrom($from)
//            ->setTo($to)
//            ->setBody($template,'text/html');
//
//        $mailer->send($message);
//    }

    public function _sendMail($message,$subject,$from,$to){
        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            ->setUsername('sujit.developer.136663@gmail.com')
            ->setPassword('maniac.sujit');

        $mailer = \Swift_Mailer::newInstance($transport);

        $message
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to);
        $mailer->send($message);
    }



}
