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

use AppBundle\Entity\User;
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
        $url = "http://student2student.co.uk/confirmRegistration/".$user->getConfirmationToken();
        $data = array(
            'user' => $user->getUsername(),
            'confirmationUrl' =>  $url,
            'header_image'=>$message->embed(\Swift_Image::fromPath('assets/images/header_big.jpg')),
            'button_activate_account_image'=>$message->embed(\Swift_Image::fromPath('assets/images/activate_button.jpg')),
            'button_tell_friends_image'=>$message->embed(\Swift_Image::fromPath('assets/images/tell_friend_button.jpg')),
            'share_image'=>$message->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
            'footer_image'=>$message->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
        );

        $message->setBody(
            $this->templating->render('mail_templates/registration_confirmation.html.twig',$data),'text/html'
        );

        $this->_sendMail($message,"Student2student.co.uk : Confirm Registration Process",$this->parameters['from_email']['confirmation'],$user->getEmail());
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {

        $message = \Swift_Message::newInstance();
        $url = "http://student2student.co.uk/resetPassword/".$user->getConfirmationToken();
        $data = array(
            'user' => $user->getUsername(),
            'confirmationUrl' =>  $url,
            'header_image'=>$message->embed(\Swift_Image::fromPath('assets/images/header_big.jpg')),
            'button_reset_password_image'=>$message->embed(\Swift_Image::fromPath('assets/images/reset.jpg')),
            'button_tell_friends_image'=>$message->embed(\Swift_Image::fromPath('assets/images/tell_friend_button.jpg')),
            'share_image'=>$message->embed(\Swift_Image::fromPath('assets/images/share.jpg')),
            'footer_image'=>$message->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
        );

        $message->setBody(
            $this->templating->render('mail_templates/reset_mail.html.twig',$data),'text/html'
        );

        $this->_sendMail($message,"student2student.co.uk : Reset Password",$this->parameters['from_email']['resetting'],$user->getEmail());

    }

    public function operateContactMailingProcess($bookDeal,$book,$seller,$buyerInfo,$buyerMessage)
    {


        if(!strcmp($bookDeal->getBookContactMethod(),"buyerToSeller")){

            if(!strcmp("On",$seller->getEmailNotification())) {
                //Sending First mail
                $message1 = \Swift_Message::newInstance();

                $toSellerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_seller_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message1->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),

                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))

                ));

                $message1->setBody($toSellerRendered, 'text/html');
                $this->_sendMail($message1, "Student2Student: Buyer Contacting You", $this->parameters['from_email']['resetting'], $seller->getEmail());
            }

            if($buyerInfo['buyerEntity'] instanceof User) {
                if (!strcmp("On", $buyerInfo['buyerEntity']->getEmailNotification())) {
                    //Sending 2nd Mail
                    $message2 = \Swift_Message::newInstance();
                    $toBuyerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_buyer_mail.html.twig", array(
                        'bookDeal' => $bookDeal,
                        'seller' => $seller,
                        'buyerInfo' => $buyerInfo,
                        'book' => $book,
                        'buyerMessage' => $buyerMessage,
                        'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                        'header_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                        'footer_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                        'envelop_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                    ));
                    $message2->setBody($toBuyerRendered, 'text/html');
                    $this->_sendMail($message2, "Student2Student: Seller Contact Information ", $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
                }
            }else{
                $message2 = \Swift_Message::newInstance();
                $toBuyerRendered = $this->templating->render("mail_templates/buyer_to_seller_method_to_buyer_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));
                $message2->setBody($toBuyerRendered, 'text/html');
                $this->_sendMail($message2, "Student2Student: Seller Contact Information ", $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
            }
        }elseif(!strcmp($bookDeal->getBookContactMethod(),"sellerToBuyer")){

            if(!strcmp("On",$seller->getEmailNotification())) {

                //Sending First Mail
                $message1 = \Swift_Message::newInstance();

                $toSellerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_seller_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message1->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));


                $message1->setBody($toSellerRendered, 'text/html');

                $this->_sendMail($message1, "Student2Student: Buyer Contact Information", $this->parameters['from_email']['resetting'], $seller->getEmail());
            }

            if($buyerInfo['buyerEntity'] instanceof User) {
                if (!strcmp("On", $buyerInfo['buyerEntity']->getEmailNotification())) {
                    //Sending 2nd Mail
                    $message2 = \Swift_Message::newInstance();

                    $toBuyerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_buyer_mail.html.twig", array(
                        'bookDeal' => $bookDeal,
                        'seller' => $seller,
                        'buyerInfo' => $buyerInfo,
                        'book' => $book,
                        'buyerMessage' => $buyerMessage,
                        'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                        'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                        'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                        'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                    ));
                    $message2->setBody($toBuyerRendered, 'text/html');
                    $this->_sendMail($message2, "Student2Student: Sent Information to Seller", $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
                }
            }else{
                $message2 = \Swift_Message::newInstance();

                $toBuyerRendered = $this->templating->render("mail_templates/seller_to_buyer_method_to_buyer_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));
                $message2->setBody($toBuyerRendered, 'text/html');
                $this->_sendMail($message2, "Student2Student: Sent Information to Seller", $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
            }
        }elseif(!strcmp($bookDeal->getBookContactMethod(),"student2studentBoard")){

            if(!strcmp("On",$seller->getEmailNotification())) {

                //Sending First Mail
                $message1 = \Swift_Message::newInstance();

                $toSellerRendered = $this->templating->render("mail_templates/student2studentBoard_method_to_seller_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message1->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));
            }

            if($buyerInfo['buyerEntity'] instanceof User) {
                if (!strcmp("On", $buyerInfo['buyerEntity']->getEmailNotification())) {
                    $message1->setBody($toSellerRendered, 'text/html');

                    $this->_sendMail($message1, "Student2Student: New Contact Received", $this->parameters['from_email']['resetting'], $seller->getEmail());

                    //Sending 2nd Mail
                    $message2 = \Swift_Message::newInstance();

                    $toBuyerRendered = $this->templating->render("mail_templates/student2studentBoard_method_to_buyer_mail.html.twig", array(
                        'bookDeal' => $bookDeal,
                        'seller' => $seller,
                        'buyerInfo' => $buyerInfo,
                        'book' => $book,
                        'buyerMessage' => $buyerMessage,
                        'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                        'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                        'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                        'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                    ));
                    $message2->setBody($toBuyerRendered, 'text/html');
                    $this->_sendMail($message2, "Student2Student: Contacted Seller ", $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
                }
            }else{
                $message2 = \Swift_Message::newInstance();

                $toBuyerRendered = $this->templating->render("mail_templates/student2studentBoard_method_to_buyer_mail.html.twig", array(
                    'bookDeal' => $bookDeal,
                    'seller' => $seller,
                    'buyerInfo' => $buyerInfo,
                    'book' => $book,
                    'buyerMessage' => $buyerMessage,
                    'book_image' => $message2->embed(\Swift_Image::fromPath(substr($book->getBookImage(), 1))),
                    'header_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg')),
                    'footer_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg')),
                    'envelop_image'=>$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'))
                ));
                $message2->setBody($toBuyerRendered, 'text/html');
                $this->_sendMail($message2, "Student2Student: Contacted Seller ", $this->parameters['from_email']['resetting'], $buyerInfo['buyerEmail']);
            }
        }

    }

    public function operateMessageMailingProcess($contact,$message,$messageType,$messageFinalArray){

        $buyerEntity = null;
        if($contact->getBuyer()!=null){
            $buyer = $contact->getBuyer()->getUsername();
            $buyerEntity = $contact->getBuyer();
        }else{
            $buyer = $contact->getBuyerNickName();
        }
        $data=array(
            'contact'=>$contact,
            'bookDeal' => $contact->getBookDeal(),
            'seller' => $contact->getBookDeal()->getSeller(),
            'buyer'=>$buyer,
            'buyerEntity'=>$buyerEntity,
            'book'=>$contact->getBookDeal()->getBook(),
            'message'=>$message,
            'messageArray'=>$messageFinalArray,
            'contactMethod'=>$contact->getBookDeal()->getBookContactMethod()
        );

        if(!strcmp($messageType,"buyerSendingToSeller")){
            $this->_buyerToSellerMessageMail($data);
        }elseif(!strcmp($messageType,"sellerSendingToBuyer")){
            $this->_sellerToBuyerMessageMail($data);
        }


    }

    public function _buyerToSellerMessageMail($data){

        $sellerMailAddress = $data['bookDeal']->getBookContactEmail()==''?$data['seller']->getEmail():$data['bookDeal']->getBookContactEmail();

        if(!strcmp("On",$data['seller']->getEmailNotification())){

            $message1 = \Swift_Message::newInstance();

            for($i=0;$i<count($data['messageArray']);$i++){
                $data['messageArray'][$i]['embedProfilePicture'] =$message1->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
            }
            $data['book_image']=$message1->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
            $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
            $data['envelop_image']=$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
            $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
            if(count($data['messageArray'])){
                $data['previous_image']=$message1->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
            }

            $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_seller.html.twig",$data);

            $message1->setBody($rendered,'text/html');
            $this->_sendMail($message1,"Student2Student: Buyer ( ".$data['buyer']." ) Sent you message",$this->parameters['from_email']['resetting'], $sellerMailAddress);
        }


        if($data['buyerEntity'] instanceof User){
            if(!strcmp("On",$data['buyerEntity']->getEmailNotification())){
                $message2 = \Swift_Message::newInstance();

                for($i=0;$i<count($data['messageArray']);$i++){
                    $data['messageArray'][$i]['embedProfilePicture'] =$message2->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
                }
                $data['book_image']=$message2->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
                $data['header_image']=$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
                $data['envelop_image']=$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
                $data['footer_image']=$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
                if(count($data['messageArray'])){
                    $data['previous_image']=$message2->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
                }

                $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_buyer.html.twig",$data);

                $message2->setBody($rendered,'text/html');
                $this->_sendMail($message2,"Student2Student: Message sent to Seller ( ".$data['seller']->getUsername()." )",$this->parameters['from_email']['resetting'], $data['contact']->getBuyerEmail());
            }
        }else{
            $message2 = \Swift_Message::newInstance();

            for($i=0;$i<count($data['messageArray']);$i++){
                $data['messageArray'][$i]['embedProfilePicture'] =$message2->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
            }
            $data['book_image']=$message2->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
            $data['header_image']=$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
            $data['envelop_image']=$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
            $data['footer_image']=$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
            if(count($data['messageArray'])){
                $data['previous_image']=$message2->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
            }

            $rendered = $this->templating->render("mail_templates/buyer_to_seller_message_mail_to_buyer.html.twig",$data);

            $message2->setBody($rendered,'text/html');
            $this->_sendMail($message2,"Student2Student: Message sent to Seller ( ".$data['seller']->getUsername()." )",$this->parameters['from_email']['resetting'], $data['contact']->getBuyerEmail());
        }

        foreach($data['messageArray'] as $messageRow){
            if(file_exists($messageRow['senderProfilePicture']))unlink($messageRow['senderProfilePicture']);
        }


    }

    public function _sellerToBuyerMessageMail($data){

        $sellerMailAddress = $data['bookDeal']->getBookContactEmail()==''?$data['seller']->getEmail():$data['bookDeal']->getBookContactEmail();

        if($data['buyerEntity'] instanceof User){
            if(!strcmp("On",$data['buyerEntity']->getEmailNotification())){
                $message1 = \Swift_Message::newInstance();

                for($i=0;$i<count($data['messageArray']);$i++){
                    $data['messageArray'][$i]['embedProfilePicture'] =$message1->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
                }
                $data['book_image']=$message1->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
                $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
                $data['envelop_image']=$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
                $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
                if(count($data['messageArray'])){
                    $data['previous_image']=$message1->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
                }

                $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_buyer.html.twig",$data);

                $message1->setBody($rendered,'text/html');
                $this->_sendMail($message1,"Student2Student: Seller ( ".$data['seller']->getUsername()." ) Sent you message",$this->parameters['from_email']['resetting'], $data['contact']->getBuyerEmail());
            }
        }else{
            $message1 = \Swift_Message::newInstance();

            for($i=0;$i<count($data['messageArray']);$i++){
                $data['messageArray'][$i]['embedProfilePicture'] =$message1->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
            }
            $data['book_image']=$message1->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
            $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
            $data['envelop_image']=$message1->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
            $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
            if(count($data['messageArray'])){
                $data['previous_image']=$message1->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
            }
            $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_buyer.html.twig",$data);

            $message1->setBody($rendered,'text/html');
            $this->_sendMail($message1,"Student2Student: Seller ( ".$data['seller']->getUsername()." ) Sent you message",$this->parameters['from_email']['resetting'], $data['contact']->getBuyerEmail());
        }


        if(!strcmp("On",$data['seller']->getEmailNotification())){

            $message2 = \Swift_Message::newInstance();

            for($i=0;$i<count($data['messageArray']);$i++){
                $data['messageArray'][$i]['embedProfilePicture'] =$message2->embed(\Swift_Image::fromPath($data['messageArray'][$i]['senderProfilePicture']));
            }
            $data['book_image']=$message2->embed(\Swift_Image::fromPath(substr($data['book']->getBookImage(),1)));
            $data['header_image']=$message2->embed(\Swift_Image::fromPath('assets/images/header.jpg'));
            $data['envelop_image']=$message2->embed(\Swift_Image::fromPath('assets/images/envelop.png'));
            $data['footer_image']=$message2->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
            if(count($data['messageArray'])){
                $data['previous_image']=$message2->embed(\Swift_Image::fromPath('assets/images/previous.jpg'));
            }
            $rendered = $this->templating->render("mail_templates/seller_to_buyer_message_mail_to_seller.html.twig",$data);

            $message2->setBody($rendered,'text/html');
            $this->_sendMail($message2,"Student2Student: Message sent to Buyer ( ".$data['buyer']." )",$this->parameters['from_email']['resetting'], $sellerMailAddress);

        }

        foreach($data['messageArray'] as $messageRow){
            if(file_exists($messageRow['senderProfilePicture']))unlink($messageRow['senderProfilePicture']);
        }

    }

    function sendContactUsEmail($data){
        $message1 = \Swift_Message::newInstance();
        $rendered = $this->templating->render("mail_templates/contact_us_email.html.twig",$data);
        $message1->setBody($rendered,'text/html');
        $this->_sendContactUsMail($message1,"Student2Student: Contact Message",$this->parameters['from_email']['resetting'], 'support@student2student.co.uk');
    }

    function sendFriendsEmail($data){
        $message1 = \Swift_Message::newInstance();

        $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header_big.jpg'));
        $data['share_image']=$message1->embed(\Swift_Image::fromPath('assets/images/share.jpg'));
        $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
        $data['join_button']=$message1->embed(\Swift_Image::fromPath('assets/images/joinButton.jpg'));
        $rendered = $this->templating->render("mail_templates/share_mail_with_friends.html.twig",$data);

        $message1->setBody($rendered,'text/html');
        $this->_sendMailToMultiple($message1,"Student2Student",$this->parameters['from_email']['resetting'],$data['friendEmails']);
    }

    function sendShareSellPageEmailToFriends($data){
        $message1 = \Swift_Message::newInstance();

        $data['header_image']=$message1->embed(\Swift_Image::fromPath('assets/images/header_big.jpg'));
        $data['share_image']=$message1->embed(\Swift_Image::fromPath('assets/images/share.jpg'));
        $data['footer_image']=$message1->embed(\Swift_Image::fromPath('assets/images/footer.jpg'));
        $data['buy_my_textbooks_button_image']=$message1->embed(\Swift_Image::fromPath('assets/images/buy_my_textbooks.jpg'));
        $data['books_stack_image']=$message1->embed(\Swift_Image::fromPath('assets/images/books_stack.jpg'));
        $data['shareUrl'] = "http://168.61.173.224:8080/Student2Student/#/".$data['username'];

        $rendered = $this->templating->render("mail_templates/share_sell_page_mail_with_friends.html.twig",$data);

        $message1->setBody($rendered,'text/html');
        $this->_sendMailToMultiple($message1,$data['username']."'s Sell Page | Student2Student",$this->parameters['from_email']['resetting'],$data['friendEmails']);
    }




    public function _sendMail($message,$subject,$from,$to){
        $transport = \Swift_SmtpTransport::newInstance('smtp.student2student.co.uk', 25)
            ->setUsername('no-reply@student2student.co.uk')
            ->setPassword('wWoc$868');
//        $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465,'ssl')
//            ->setUsername('sujit.developer.136661@gmail.com')
//            ->setPassword('maniac.sujit');

        $mailer = \Swift_Mailer::newInstance($transport);

        $message
            ->setSubject($subject)
            ->setFrom('no-reply@student2student.co.uk')
            ->setTo($to);
        $mailer->send($message);
    }
    public function _sendContactUsMail($message,$subject,$from,$to){
        $transport = \Swift_SmtpTransport::newInstance('smtp.student2student.co.uk', 25)
            ->setUsername('no-reply@student2student.co.uk')
            ->setPassword('wWoc$868');

        $mailer = \Swift_Mailer::newInstance($transport);

        $message
            ->setSubject($subject)
            ->setFrom('no-reply@student2student.co.uk')
            ->setTo($to);
        $mailer->send($message);
    }
    public function _sendMailToMultiple($message,$subject,$from,$toes){
        $transport = \Swift_SmtpTransport::newInstance('smtp.student2student.co.uk', 25)
            ->setUsername('no-reply@student2student.co.uk')
            ->setPassword('wWoc$868');

        $mailer = \Swift_Mailer::newInstance($transport);

        $message
            ->setSubject($subject)
            ->setFrom('no-reply@student2student.co.uk');

        foreach($toes as $to){
            $message->addBcc($to['email']);
        }
        $message->setTo('no-reply@student2student.co.uk');

        $mailer->send($message);
    }


}
