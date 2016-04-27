<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookImage;
use AppBundle\Entity\Campus;
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
class BookDealManagementApiController extends Controller
{


    /**
     * Get Books I Have Contacted For
     */
    public function getBooksIHaveContactedForAction(Request $request)
    {

        $deals=array(
            'buyerToSeller'=>array(),
            'sellerToBuyer'=>array()
        );

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals =$bookDealRepo->getBooksIHaveContactedFor($userId);

        foreach($bookDeals as $deal){

            //Formatting Date
            if(array_key_exists('bookPublishDate',$deal)){
                $deal['bookPublishDate']=$deal['bookPublishDate']->format('d M Y');
            }
            if($deal['bookAvailableDate']!=null){
                $deal['bookAvailableDate']=$deal['bookAvailableDate']->format('d M Y');
            }

            if($deal['contactDateTime']!=null){
                $deal['contactDateTime']=$deal['contactDateTime']->format('d M Y');
            }

            //dividing via Contact Method
            if(strpos('buyerToSeller',$deal['bookContactMethod'])!==false){
                array_push($deals['buyerToSeller'],$deal);
            }else{
                array_push($deals['sellerToBuyer'],$deal);
            }

        }


        return $this->_createJsonResponse('success',array(
            'successData'=>$deals
        ),200);
    }

    /**
     * Get Books I Have Created For
     */

    public function getBooksIHaveCreatedAction(Request $request){
        $deals=array(
            'buyerToSeller'=>array(),
            'sellerToBuyer'=>array()
        );

        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');
        $userRepo = $em->getRepository('AppBundle:User');
        $bookDeals =$bookDealRepo->getBooksIHaveCreated($userId);

        //Getting Contacts of Deals
        $contacts = $bookDealRepo->getContactsOfBookDeals($bookDeals);

        for($i=0;$i<count($bookDeals); $i++){
            $bookDeals[$i]['contacts']=array();
        }

        //Adding Contacts according to deals
        foreach($contacts as $contact){

            for($i=0;$i<count($bookDeals); $i++){
                if((int)$contact['bookDealId']==(int)$bookDeals[$i]['bookDealId']){

                    if($contact['buyerNickName']==null){
                        $user = $userRepo->findById((int)$contact['buyerId']);
                        $contact['buyerNickName']= $user[0]->getUsername();
                    }
                    $contact['contactDateTime']=$contact['contactDateTime']->format('H:i d M Y');
                    array_push($bookDeals[$i]['contacts'],$contact);
                }
            }

        }

        //Getting Deals I have created
        foreach($bookDeals as $deal){

            //Formatting Date
            if(array_key_exists('bookPublishDate',$deal)){
                $deal['bookPublishDate']=$deal['bookPublishDate']->format('d M Y');
            }
            if($deal['bookAvailableDate']!=null){
                $deal['bookAvailableDate']=$deal['bookAvailableDate']->format('d M Y');
            }

            //dividing via Contact Method
            if(strpos('buyerToSeller',$deal['bookContactMethod'])!==false){
                array_push($deals['buyerToSeller'],$deal);
            }else{
                array_push($deals['sellerToBuyer'],$deal);
            }

        }

        return $this->_createJsonResponse('success',array(
            'successData'=>$deals
        ),200);
    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
