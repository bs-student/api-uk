<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookDeal;
use AppBundle\Entity\BookImage;
use AppBundle\Entity\Campus;
use AppBundle\Entity\WishList;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\UniversityType;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\WishListType;
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

class WishListManagementApiController extends Controller
{

    /**
     * Add book into WishList
     */
    public function addBookToWishListAction(Request $request){

        $em = $this->getDoctrine()->getManager();
        $wishListRepo = $em->getRepository("AppBundle:WishList");

        $content = $request->getContent();
        $data = json_decode($content, true);

        $user = $this->get('security.token_storage')->getToken()->getUser();


        $alreadyInserted = $wishListRepo->checkIfAlreadyAddedToWishList($user->getId(),$data['bookId']);

        if(!$alreadyInserted){
            $wishList = new WishList();
            $wishListForm = $this->createForm(new WishListType(), $wishList);
            $wishListForm->submit(array(
                'user'=>$user->getId(),
                'book'=>$data['bookId'],
            ));

            if ($wishListForm->isValid()) {
                $em->persist($wishList);
                $em->flush();
                return $this->_createJsonResponse('success', array("successTitle" => "Book Successfully Added to WishList"), 200);
            } else {
                return $this->_createJsonResponse('error', array("errorTitle" => "Couldn't Added to Wishlist","errorData" => $wishListForm), 400);
            }
        }else{
            return $this->_createJsonResponse('error', array("errorTitle" => "Book is already in Wishlist"), 400);
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
