<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookImage;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Contact;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\UniversityType;
use Doctrine\Common\Collections\ArrayCollection;


use FOS\UserBundle\Entity\User;
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

class BookDealManagementApiController extends Controller
{


    /**
     * Get Books I Have Contacted For
     */
    public function getBooksIHaveContactedForAction(Request $request)
    {

        $deals = array(
            'buyerToSeller' => array(),
            'sellerToBuyer' => array()
        );

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals = $bookDealRepo->getBooksIHaveContactedFor($userId);


        //Set Subtitle in Book
        for ($i = 0; $i < count($bookDeals); $i++) {
            $bookDeals[$i]['contacts'] = array();
            if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
            }

        }

        foreach ($bookDeals as $deal) {


            //Formatting Date
            if (array_key_exists('bookPublishDate', $deal)) {
                $deal['bookPublishDate'] = $deal['bookPublishDate']->format('d M Y');
            }
            if ($deal['bookAvailableDate'] != null) {
                $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
            }

            if ($deal['contactDateTime'] != null) {
                $deal['contactDateTime'] = $deal['contactDateTime']->format('d M Y');
            }

            //Formatting Contact
            array_push($deal['contacts'],array(
                'contactDateTime'=>$deal['contactDateTime'],
                'contactId' =>$deal['contactId']
            ));



            //Getting Images
            $images = array();
            $bookDeal = $bookDealRepo->findOneById($deal['bookDealId']);
            //GET FIRST IMAGE OF THAT BOOK
            array_push($images,array(
                'image'=>$deal['bookImage'],
                'imageId'=>0
            ));

            $bookDealImages = $bookDeal->getBookDealImages();
            for($i=0;$i<count($bookDealImages);$i++){
                array_push($images,array(
                    'image'=>$bookDealImages[$i]->getImageUrl(),
                    'imageId'=>($i+1)
                ));
            }
            $deal['bookImages']=$images;


            //dividing via Contact Method
            if (strpos('buyerToSeller', $deal['bookContactMethod']) !== false) {
                array_push($deals['buyerToSeller'], $deal);
            } else {
                array_push($deals['sellerToBuyer'], $deal);
            }


        }


        return $this->_createJsonResponse('success', array(
            'successData' => $deals
        ), 200);
    }

    /**
     * Get Books I Have Created For
     */
    public function getBooksIHaveCreatedAction(Request $request)
    {
        $deals = array(
            'buyerToSeller' => array(),
            'sellerToBuyer' => array()
        );

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals = $bookDealRepo->getBooksIHaveCreated($userId);

        //Getting Contacts of Deals
        $contacts = $bookDealRepo->getContactsOfBookDeals($bookDeals);


        //Set Subtitle in Book
        for ($i = 0; $i < count($bookDeals); $i++) {
            $bookDeals[$i]['contacts'] = array();
            if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
            }

        }

        //Adding Contacts according to deals
        foreach ($contacts as $contact) {

            for ($i = 0; $i < count($bookDeals); $i++) {
                if ((int)$contact['bookDealId'] == (int)$bookDeals[$i]['bookDealId']) {

                    if ($contact['buyerNickName'] == null) {
                        $user = $userRepo->findById((int)$contact['buyerId']);
                        $contact['buyerNickName'] = $user[0]->getUsername();
                    }
                    $contact['contactDateTime'] = $contact['contactDateTime']->format('H:i d M Y');
                    array_push($bookDeals[$i]['contacts'], $contact);
                }
            }

        }

        //Getting Deals I have created
        foreach ($bookDeals as $deal) {

            //Formatting Date
            if (array_key_exists('bookPublishDate', $deal)) {
                $deal['bookPublishDate'] = $deal['bookPublishDate']->format('d M Y');
            }
            if ($deal['bookAvailableDate'] != null) {
                $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
            }

            //Getting Images
            $images = array();
            $bookDeal = $bookDealRepo->findOneById($deal['bookDealId']);
            //GET FIRST IMAGE OF THAT BOOK
            array_push($images,array(
                'image'=>$deal['bookImage'],
                'imageId'=>0
            ));

            $bookDealImages = $bookDeal->getBookDealImages();
            for($i=0;$i<count($bookDealImages);$i++){
                array_push($images,array(
                    'image'=>$bookDealImages[$i]->getImageUrl(),
                    'imageId'=>($i+1)
                ));
            }
            $deal['bookImages']=$images;


            //dividing via Contact Method
            if (strpos('buyerToSeller', $deal['bookContactMethod']) !== false) {
                array_push($deals['buyerToSeller'], $deal);
            } else {
                array_push($deals['sellerToBuyer'], $deal);
            }

        }

        return $this->_createJsonResponse('success', array(
            'successData' => $deals
        ), 200);
    }

    /**
     * Sell Book to A User
     */
    public function sellBookToUserAction(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);
        $em = $this->getDoctrine()->getManager();
        $contactRepo = $em->getRepository('AppBundle:Contact');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();

        //Check If contact Id exist
        if (array_key_exists('contactId', $data)) {

            $contact = $contactRepo->findOneById($data['contactId']);

            if($contact instanceof Contact){

                $bookDeal = $contact->getBookDeal();

                //IF User is the owner of that deal and deal is activated
                if ($bookDeal->getSeller()->getId() == $userId && (!strcmp($bookDeal->getBookStatus(),'Activated'))) {

                    $bookDealData=array(
                        'bookSellingStatus'=>"Sold"
                    );

                    if (($contact->getBuyer() instanceof User)) {
                        //Sell the book by buyer Id
                        $bookDealData['buyer']=$contact->getBuyer()->getId();
                        $buyerName = $contact->getBuyer()->getusername();
                    }elseif($contact->getBuyer()==null){
                        $buyerName = $contact->getBuyerNickName();
                    }

                    // Update Book Deal
                    $bookDealForm = $this->createForm(new BookDealType(), $bookDeal);
                    $bookDealForm->remove('book');
                    $bookDealForm->remove('bookPriceSell');
                    $bookDealForm->remove('bookCondition');
                    $bookDealForm->remove('bookIsHighlighted');
                    $bookDealForm->remove('bookHasNotes');
                    $bookDealForm->remove('bookComment');
                    $bookDealForm->remove('bookContactMethod');
                    $bookDealForm->remove('bookContactHomeNumber');
                    $bookDealForm->remove('bookContactCellNumber');
                    $bookDealForm->remove('bookContactEmail');
                    $bookDealForm->remove('bookIsAvailablePublic');
                    $bookDealForm->remove('bookPaymentMethodCaShOnExchange');
                    $bookDealForm->remove('bookPaymentMethodCheque');
                    $bookDealForm->remove('bookAvailableDate');
                    $bookDealForm->remove('seller');
                    $bookDealForm->remove('bookStatus');
                    $bookDealForm->remove('bookViewCount');
                    $bookDealForm->remove('bookDealImages');

                    $bookDealForm->submit($bookDealData);


                    $contactForm = $this->createForm(new ContactType(), $contact);
                    $contactForm ->remove('buyerNickName');
                    $contactForm ->remove('buyerEmail');
                    $contactForm ->remove('buyerHomePhone');
                    $contactForm ->remove('buyerCellPhone');
                    $contactForm ->remove('bookDeal');
                    $contactForm ->remove('buyer');
                    $contactForm ->remove('messages');
                    $contactForm ->remove('contactDateTime');

                    $contactData=array(
                        'soldToThatBuyer'=>"Yes"
                    );

                    $contactForm->submit($contactData);

                    if ($bookDealForm->isValid() && $contactForm->isValid()) {
                        $em->persist($bookDeal);
                        $em->persist($contact);
                        $em->flush();
                        return $this->_createJsonResponse('success', array(
                            'successTitle' => "Book Sold to ".$buyerName
                        ), 200);
                    } else {
                        return $this->_createJsonResponse('error', array("errorTitle"=>"Could Not Sell The Book","errorData" => array($bookDealForm,$contactForm)), 400);
                    }



                }else{
                    return $this->_createJsonResponse('error',array(
                        'errorTitle'=>'Cannot Sell Book',
                        'errorDescription'=>"You Didn't post that deal or Book is deactivated right now"
                    ),400);
                }
            }else{
                return $this->_createJsonResponse('error',array(
                    'errorTitle'=>'Cannot Sell Book',
                    'errorDescription'=>'Check The Form and Submit Again'
                ),400);
            }

        }else{
            return $this->_createJsonResponse('error',array(
                'errorTitle'=>'Cannot Sell Book',
                'errorDescription'=>'Check The Form and Submit Again'
            ),400);
        }


    }

    /**
     * Get Books I Have Created For and Sold (Sell Archive)
     */
    public function getBooksIHaveCreatedAndSoldAction(Request $request){
        $deals = array(
            'buyerToSeller' => array(),
            'sellerToBuyer' => array()
        );

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals = $bookDealRepo->getBooksIHaveCreatedAndSold($userId);

        //Getting Contacts of Deals
        $contacts = $bookDealRepo->getContactsOfBookDeals($bookDeals);

        //Set Subtitle in Book
        for ($i = 0; $i < count($bookDeals); $i++) {
            $bookDeals[$i]['contacts'] = array();
            if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
            }

        }

        //Adding Contacts according to deals
        foreach ($contacts as $contact) {

            for ($i = 0; $i < count($bookDeals); $i++) {
                if ((int)$contact['bookDealId'] == (int)$bookDeals[$i]['bookDealId']) {

                    if ($contact['buyerNickName'] == null) {
                        $user = $userRepo->findById((int)$contact['buyerId']);
                        $contact['buyerNickName'] = $user[0]->getUsername();
                    }
                    $contact['contactDateTime'] = $contact['contactDateTime']->format('H:i d M Y');
                    array_push($bookDeals[$i]['contacts'], $contact);
                }
            }

        }

        //Getting Deals I have created
        foreach ($bookDeals as $deal) {


            //Getting Buyer
            if($deal['buyerId']!=null){
                $buyer = $userRepo->findOneById($deal['buyerId']);
                $deal['buyerNickName']=$buyer->getUsername();
            }else{
                $buyer = $bookDealRepo->getPublicUserWhoBoughtBookDeal($deal['bookDealId']);
                $deal['buyerNickName']=$buyer[0]['buyerNickName'];

            }


            //Formatting Date
            if (array_key_exists('bookPublishDate', $deal)) {
                $deal['bookPublishDate'] = $deal['bookPublishDate']->format('d M Y');
            }
            if ($deal['bookAvailableDate'] != null) {
                $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
            }

            //Getting Images
            $images = array();
            $bookDeal = $bookDealRepo->findOneById($deal['bookDealId']);
            //GET FIRST IMAGE OF THAT BOOK
            array_push($images,array(
                'image'=>$deal['bookImage'],
                'imageId'=>0
            ));

            $bookDealImages = $bookDeal->getBookDealImages();
            for($i=0;$i<count($bookDealImages);$i++){
                array_push($images,array(
                    'image'=>$bookDealImages[$i]->getImageUrl(),
                    'imageId'=>($i+1)
                ));
            }
            $deal['bookImages']=$images;


            //dividing via Contact Method
            if (strpos('buyerToSeller', $deal['bookContactMethod']) !== false) {
                array_push($deals['buyerToSeller'], $deal);
            } else {
                array_push($deals['sellerToBuyer'], $deal);
            }

        }

        return $this->_createJsonResponse('success', array(
            'successData' => $deals
        ), 200);
    }

    /**
     * Get Books I Have Have Bought (Buy Archive)
     */
    public function getBooksIHaveBoughtAction(Request $request){
        $deals = array(
            'buyerToSeller' => array(),
            'sellerToBuyer' => array()
        );

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals = $bookDealRepo->getBooksIHaveBought($userId);

        //Getting Contacts of Deals
//        $contacts = $bookDealRepo->getContactsOfBookDeals($bookDeals);


        //Set Subtitle in Book
        for ($i = 0; $i < count($bookDeals); $i++) {
            $bookDeals[$i]['contacts'] = array();
            if (strpos($bookDeals[$i]['bookTitle'], ":")) {
                $bookDeals[$i]['bookSubTitle'] = substr($bookDeals[$i]['bookTitle'], strpos($bookDeals[$i]['bookTitle'], ":") + 2);
                $bookDeals[$i]['bookTitle'] = substr($bookDeals[$i]['bookTitle'], 0, strpos($bookDeals[$i]['bookTitle'], ":"));
            }

        }


        //Adding Contacts according to deals
//        foreach ($contacts as $contact) {
//
//            for ($i = 0; $i < count($bookDeals); $i++) {
//                if ((int)$contact['bookDealId'] == (int)$bookDeals[$i]['bookDealId']) {
//
//                    if ($contact['buyerNickName'] == null) {
//                        $user = $userRepo->findById((int)$contact['buyerId']);
//                        $contact['buyerNickName'] = $user[0]->getUsername();
//                    }
//                    $contact['contactDateTime'] = $contact['contactDateTime']->format('H:i d M Y');
//                    array_push($bookDeals[$i]['contacts'], $contact);
//                }
//            }
//
//        }

        //Getting Deals I have created
        foreach ($bookDeals as $deal) {


            //Getting Buyer
            if($deal['buyerId']!=null){
                $buyer = $userRepo->findOneById($deal['buyerId']);
                $deal['buyerNickName']=$buyer->getUsername();
            }else{
                $buyer = $bookDealRepo->getPublicUserWhoBoughtBookDeal($deal['bookDealId']);
                $deal['buyerNickName']=$buyer[0]['buyerNickName'];

            }


            //Formatting Date
            if (array_key_exists('bookPublishDate', $deal)) {
                $deal['bookPublishDate'] = $deal['bookPublishDate']->format('d M Y');
            }
            if ($deal['bookAvailableDate'] != null) {
                $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
            }

            //Getting Images
            $images = array();
            $bookDeal = $bookDealRepo->findOneById($deal['bookDealId']);
            //GET FIRST IMAGE OF THAT BOOK
            array_push($images,array(
                'image'=>$deal['bookImage'],
                'imageId'=>0
            ));

            $bookDealImages = $bookDeal->getBookDealImages();
            for($i=0;$i<count($bookDealImages);$i++){
                array_push($images,array(
                    'image'=>$bookDealImages[$i]->getImageUrl(),
                    'imageId'=>($i+1)
                ));
            }
            $deal['bookImages']=$images;

            //Formatting Contact
            array_push($deal['contacts'],array(
                'contactDateTime'=>$deal['contactDateTime'],
                'contactId' =>$deal['contactId']
            ));


            //dividing via Contact Method
            if (strpos('buyerToSeller', $deal['bookContactMethod']) !== false) {
                array_push($deals['buyerToSeller'], $deal);
            } else {
                array_push($deals['sellerToBuyer'], $deal);
            }

        }


        return $this->_createJsonResponse('success', array(
            'successData' => $deals
        ), 200);
    }

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
