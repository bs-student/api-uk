<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookImage;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Message;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\MessageType;
use AppBundle\Form\Type\UniversityType;
use Doctrine\Common\Collections\ArrayCollection;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Form\Type\CampusType;
use AppBundle\Entity\University;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Lsw\ApiCallerBundle\Call\HttpGetJson;
use Lsw\ApiCallerBundle\Call\HttpGetHtml;
use AppBundle\Form\Type\BookType;
use Symfony\Component\HttpFoundation\FileBag;
use AppBundle\Entity\Contact;
class ContactManagementApiController extends Controller
{
    /**
     * Add New Contact Api Action
     */
    public function addNewContactApiAction(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);


        if (array_key_exists('contact', $data)) {


                    $em = $this->getDoctrine()->getManager();
                    $contactRepo = $em->getRepository("AppBundle:Contact");
                    $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();

                    //Check If Already Contacted
                    if(!$contactRepo->alreadyContacted($data['contact'],$userId)){
                        //Save the Contact With Message

                        $data['contact']['contactDateTime'] =  gmdate('Y-m-d H:i:s');
                        $data['contact']['messages'][0]['messageDateTime'] =  gmdate('Y-m-d H:i:s');
                        $data['contact']['messages'][0]['user'] =  $userId;
                        $data['contact']['buyer']=$userId;


                        $contact = new Contact();

                        $contactForm = $this->createForm(new ContactType(), $contact);
                        $data['contact']['soldToThatBuyer']="No";
                        $data['contact']['contactCondition']="New";
                        $contactForm->submit($data['contact']);

                        if($contactForm->isValid()){
                            $em->persist($contact);
                            $em->flush();

                            //Prepare Proper Message
                            $bookDealRepo = $em->getRepository("AppBundle:BookDeal");
                            $bookDeals = $bookDealRepo->findById($data['contact']['bookDeal']);

                            if(!strcmp($bookDeals[0]->getBookContactMethod(),"buyerToSeller")){
                                $message="We have sent you ".$bookDeals[0]->getSeller()->getUsername()."'s contact information over your mail. Please contact ".$bookDeals[0]->getSeller()->getUsername();
                                $buyerInfo=array(
                                    'buyerNickName'=>$this->get('security.token_storage')->getToken()->getUser()->getUsername(),
                                    'buyerEmail'=>$data['contact']['buyerEmail']
                                );
                            }elseif(!strcmp($bookDeals[0]->getBookContactMethod(),"sellerToBuyer")){
                                $message="We have sent ".$bookDeals[0]->getSeller()->getUsername()." your contact information. ".$bookDeals[0]->getSeller()->getUsername()." will contact you as soon as possible.";
                                $buyerInfo=array(
                                    'buyerNickName'=>$this->get('security.token_storage')->getToken()->getUser()->getUsername(),
                                    'buyerEmail'=>$data['contact']['buyerEmail']
                                );
                                if(array_key_exists('buyerCellPhone',$data['contact'])){
                                    $buyerInfo['buyerCellPhone']=$data['contact']['buyerCellPhone'];
                                }else{
                                    $buyerInfo['buyerCellPhone']="";
                                }
                                if(array_key_exists('buyerHomePhone',$data['contact'])){
                                    $buyerInfo['buyerHomePhone']=$data['contact']['buyerHomePhone'];
                                }else{
                                    $buyerInfo['buyerHomePhone']="";
                                }

                            }elseif(!strcmp($bookDeals[0]->getBookContactMethod(),"student2studentBoard")){
                                $message="You have successfully contacted ".$bookDeals[0]->getSeller()->getUsername().". Please go to message board to view replies.";
                                $buyerInfo=array(
                                    'buyerNickName'=>$this->get('security.token_storage')->getToken()->getUser()->getUsername(),
                                    'buyerEmail'=>$this->get('security.token_storage')->getToken()->getUser()->getEmail()
                                );
                            }
                            //Send Proper Mails to Buyer & Seller
                            $this->get('fos_user.mailer')->operateContactMailingProcess($bookDeals[0],$bookDeals[0]->getBook(),$bookDeals[0]->getSeller(),$buyerInfo,$data['contact']['messages'][0]);

                            return $this->_createJsonResponse('success',array(
                                'successTitle'=>"Successfully Contacted ".$bookDeals[0]->getSeller()->getUsername(),
                                'successDescription'=>$message,
                                'successData'=>array('contactId'=>$contact->getId())
                            ),201);
                        }else{

                            return $this->_createJsonResponse('error',array('errorTitle'=>"Could not contact","errorDescription"=>"Sorry, we could not contact. Please Try again. ","errorData"=>$contactForm),200);

                        }
                    }else{
                        return $this->_createJsonResponse('error', array(
                            'errorTitle' => "Already Contacted Earlier",
                            'errorDescription' => "Please Check your already contacted Book list."
                        ), 400);
                    }


        } else {
            return $this->_createJsonResponse('error', array(
                'errorTitle' => "Wrong data",
                'errorDescription' => "Please reload and send valid data again."
            ), 400);
        }


    }

    /**
     * Add New Contact Action
     */
    public function addNewContactAction(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);


        if (array_key_exists('contact', $data)) {

//            if (array_key_exists('key', $data['contact'])) {
//                $captchaApiInfo = $this->getParameter('google_re_captcha_info');
//
//                $host = $captchaApiInfo['host'];
//                $secret = $captchaApiInfo['secret'];
//
//                $url = $host . "?secret=" . $secret . "&response=" . $data['contact']['key'];
//
//                $jsonOutput = $this->container->get('api_caller')->call(new HttpGetHtml($url, null, null));
//                $captchaResponse = json_decode($jsonOutput, true);
//                if ($captchaResponse['success']) {

            $em = $this->getDoctrine()->getManager();


            //Save the Contact With Message
            $data['contact']['contactDateTime'] =  gmdate('Y-m-d H:i:s');
            $data['contact']['messages'][0]['messageDateTime'] =  gmdate('Y-m-d H:i:s');
            $contact = new Contact();

            $contactForm = $this->createForm(new ContactType(), $contact);
            $data['contact']['soldToThatBuyer']="No";
            $data['contact']['contactCondition']="New";
            $contactForm->submit($data['contact']);

            if($contactForm->isValid()){
                $em->persist($contact);
                $em->flush();

                //Prepare Proper Message
                $bookDealRepo = $em->getRepository("AppBundle:BookDeal");
                $bookDeals = $bookDealRepo->findById($data['contact']['bookDeal']);

                if(!strcmp($bookDeals[0]->getBookContactMethod(),"buyerToSeller")){
                    $message="We have sent you ".$bookDeals[0]->getSeller()->getUsername()."'s contact information over your mail. Please contact ".$bookDeals[0]->getSeller()->getUsername();
                    $buyerInfo=array(
                        'buyerEmail'=>$data['contact']['buyerEmail']
                    );
                    if(array_key_exists('buyerNickName',$data['contact']))$buyerInfo['buyerNickName']=$data['contact']['buyerNickName'];

                }elseif(!strcmp($bookDeals[0]->getBookContactMethod(),"sellerToBuyer")){
                    $message="We have sent ".$bookDeals[0]->getSeller()->getUsername()." your contact information. ".$bookDeals[0]->getSeller()->getUsername()." will contact you as soon as possible.";
                    $buyerInfo=array(
                        'buyerEmail'=>$data['contact']['buyerEmail']
                    );
                    if(array_key_exists('buyerNickName',$data['contact']))$buyerInfo['buyerNickName']=$data['contact']['buyerNickName'];
                    if(array_key_exists('buyerCellPhone',$data['contact']))$buyerInfo['buyerCellPhone']=$data['contact']['buyerCellPhone'];
                    if(array_key_exists('buyerHomePhone',$data['contact']))$buyerInfo['buyerHomePhone']=$data['contact']['buyerHomePhone'];
                }

                //Send Proper Mails to Buyer & Seller
                $this->get('fos_user.mailer')->operateContactMailingProcess($bookDeals[0],$bookDeals[0]->getBook(),$bookDeals[0]->getSeller(),$buyerInfo,$data['contact']['messages'][0]);


                return $this->_createJsonResponse('success',array(
                    'successTitle'=>"Successfully Contacted ".$bookDeals[0]->getSeller()->getUsername(),
                    'successDescription'=>$message,
                    'successData'=>array('contactId'=>$contact->getId())
                ),201);
            }else{

                return $this->_createJsonResponse('error',array('errorTitle'=>"Could not contact","errorDescription"=>"Sorry, we could not contact. Please Try again. ","errorData"=>$contactForm),200);

            }


//                }else {
//                    return $this->_createJsonResponse('error', array(
//                        'errorTitle' => "Getting Data was not Possible",
//                        'errorDescription' => "Captcha was Wrong. Reload and try again."
//                    ), 400);
//                }
//            } else {
//                return $this->_createJsonResponse('error', array(
//                    'errorTitle' => "Getting Data was not Possible",
//                    'errorDescription' => "Captcha was Wrong. Reload and try again."
//                ), 400);
//            }

        } else {
            return $this->_createJsonResponse('error', array(
                'errorTitle' => "Wrong data",
                'errorDescription' => "Please reload and send valid data again."
            ), 400);
        }
    }

    /**
     * get Messages of A deal
     */
    public function getMessagesAction(Request $request){
        $content = $request->getContent();
        $data = json_decode($content, true);
        $em = $this->getDoctrine()->getManager();
        $contactRepo = $em->getRepository("AppBundle:Contact");

        //Fetch Messages
        if (array_key_exists('contactId', $data)) {
            $contact = $contactRepo ->findById($data['contactId']);
            $messages =$contact[0]->getMessages();
            $data=array();

            //Format Message Array
            foreach($messages as $message){
                if($message->getUser()==null){
                    $sender = $contact[0]->getBuyerNickName();
                }else{
                    $sender = $message->getUser()->getUsername();
                }
                array_push($data,array(
                    'messageId'=>$message->getId(),
                    'sender'=> $sender,
                    'messageDateTime'=> $message->getMessageDateTime()->format('H:i, d-M-Y'),
                    'messageBody'=> $message->getMessageBody(),
                ));
            }
            return $this->_createJsonResponse('success', array(
                'successData' => $data
            ), 200);
        }else{
            return $this->_createJsonResponse('error', array(
                'errorTitle' => "Wrong data",
                'errorDescription' => "Please reload and send valid data again."
            ), 400);
        }
    }

    /**
     * Send Messages of A deal with Email
     */
    public function sendMessagesAction(Request $request){
        $content = $request->getContent();
        $data = json_decode($content, true);
        $em = $this->getDoctrine()->getManager();
        $contactRepo = $em->getRepository("AppBundle:Contact");
        $userRepo = $em->getRepository("AppBundle:User");
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        if(array_key_exists('contactId',$data)){

            $contact  =$contactRepo->findById($data['contactId']);
            $message = new Message();
            $message->setContact($contact[0]);
            $messageForm = $this->createForm(new MessageType(), $message);
            $data['user']=$userId;
            $data['messageDateTime']= gmdate('Y-m-d H:i:s');
            $data['messageBody']= $data['message'];
            $messageForm->submit($data);

            if($messageForm->isValid()){
                $em->persist($message);
                $em->flush();


                //Find Out What Type of message is sent (buyerToSeller/sellerToBuyer)
                $messageType = $this->_whatTypeOfMessage($contact[0],$userId);

                //Send Proper Mails
                $this->get('fos_user.mailer')->operateMessageMailingProcess($contact[0],$message,$messageType);

                return $this->_createJsonResponse('success',array(
                    'successTitle'=>"Successfully Sent Message",
                    'successData'=>array(
                        'sender'=>$message->getUser()->getUsername(),
                        'messageBody'=>$message->getMessageBody(),
                        'messageDateTime'=>$message->getMessageDateTime()->format('H:i, d-M-Y'),
                        'messageId'=>$message->getId()
                    )
                ),201);
            }else{
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => "Cannot Send Message",
                    'errorDescription' => "Please reload and send valid data again.",
                    'errorData'=>$messageForm
                ), 400);
            }
        }else{
            return $this->_createJsonResponse('error', array(
                'errorTitle' => "Wrong data",
                'errorDescription' => "Please reload and send valid data again."
            ), 400);

        }


    }

    /**
     * Send Messages of A deal without Email
     */
    public function sendMessagesWithoutMailingAction(Request $request){
        $content = $request->getContent();
        $data = json_decode($content, true);
        $em = $this->getDoctrine()->getManager();
        $contactRepo = $em->getRepository("AppBundle:Contact");
        $userRepo = $em->getRepository("AppBundle:User");
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        if(array_key_exists('contactId',$data)){

            $contact  =$contactRepo->findById($data['contactId']);
            $message = new Message();
            $message->setContact($contact[0]);
            $messageForm = $this->createForm(new MessageType(), $message);
            $data['user']=$userId;
            $data['messageDateTime']= gmdate('Y-m-d H:i:s');
            $data['messageBody']= $data['message'];
            $messageForm->submit($data);

            if($messageForm->isValid()){
                $em->persist($message);
                $em->flush();

                return $this->_createJsonResponse('success',array(
                    'successTitle'=>"Successfully Sent Message",
                    'successData'=>array(
                        'sender'=>$message->getUser()->getUsername(),
                        'messageBody'=>$message->getMessageBody(),
                        'messageDateTime'=>$message->getMessageDateTime()->format('H:i, d-M-Y'),
                        'messageId'=>$message->getId()
                    )
                ),201);
            }else{
                return $this->_createJsonResponse('error', array(
                    'errorTitle' => "Cannot Send Message",
                    'errorDescription' => "Please reload and send valid data again.",
                    'errorData'=>$messageForm
                ), 400);
            }
        }else{
            return $this->_createJsonResponse('error', array(
                'errorTitle' => "Wrong data",
                'errorDescription' => "Please reload and send valid data again."
            ), 400);

        }


    }


    public function _whatTypeOfMessage($contact,$senderId){
        if($contact->getBuyer()!=null){
            if($contact->getBuyer()->getId()==$senderId){
                return "buyerSendingToSeller";
            }elseif($contact->getBookDeal()->getSeller()->getId()==$senderId){
                return "sellerSendingToBuyer";
            }
        }else{
            return "sellerSendingToBuyer";
        }

    }
    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
