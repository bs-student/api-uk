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
class BookManagementApiController extends Controller
{


    /**
     * Search By Keyword Amazon Api
     */
    public function searchByKeywordAmazonApiAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (array_key_exists('keyword', $data)) {
            $keyword = $data['keyword'];
        } else {
            $keyword = null;
        }
        if (array_key_exists('page', $data)) {
            $page = $data['page'];
        } else {
            $page = null;
        }
        return $this->_getBooksByKeywordAmazon($keyword, $page);

    }


    /**
     * Search By ASIN Amazon API
     */
    public function searchByAsinAmazonApiAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (array_key_exists('asin', $data)) {
            $asin = $data['asin'];
        } else {
            $asin = "";
        }

        return $this->_getBooksByAsinAmazon($asin);

    }

    /**
     * Search Book By ISBN Amazon API
     */
    public function searchByIsbnAmazonApiAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (array_key_exists('isbn', $data)) {
            $isbn = $data['isbn'];
        } else {
            $isbn = "";
        }

        return $this->_getBooksByIsbnAmazon($isbn);

    }


    /**
     * Search By ISBN Campus Books APi
     */
    public function searchByIsbnCampusBooksApiAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (array_key_exists('isbn', $data)) {
            $isbn = $data['isbn'];
        } else {
            $isbn = "";
        }

        return $this->_getBooksByIsbnCampusBooks($isbn);

    }

    /**
     * Get Amazon Cart Create Url
     */
    public function getAmazonCartCreateUrlAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (array_key_exists('bookOfferId', $data)) {
            $bookOfferId = $data['bookOfferId'];
        } else {
            $bookOfferId= "";
        }

        $addToCartAmazonUrl = $this->_addToCartAmazonUrl($bookOfferId);
        $xmlOutput = $this->get('api_caller')->call(new HttpGetHtml($addToCartAmazonUrl, null, null));


        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xmlOutput);

        $fileContents = trim(str_replace('"', "'", $fileContents));

        $simpleXml = simplexml_load_string($fileContents);


        return $this->_createJsonResponse('success',array('successData'=>array('cartUrl'=>(string)$simpleXml->Cart->PurchaseURL)),200);

    }
    /**
     * Sell New Book
     */
    public function addNewSellBookAction(Request $request)
    {

        $serializer = $this->container->get('jms_serializer');
        $userId = $this->get('security.token_storage')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $fileDirHost = $this->container->getParameter('kernel.root_dir');
        $fileDir = '/../web/bookImages/';


        $content = $request->get('book');
        $bookData = json_decode($content, true);
        $bookData['bookImages'] = array();


        $titleImageDone=false;

        $files = $request->files;
        $fileUploadError= false;
        $book = new Book();

        //Add Title Image from Amazon
        if(array_key_exists('bookLargeImageUrl',$bookData)){
            $imageOutput = $this->get('api_caller')->call(new HttpGetHtml(json_decode($request->get('book'),true)['bookLargeImageUrl'], null, null));

            $fileSaveName = gmdate("Y-d-m_h_i_s_").rand(0,99999999).".png";
            $fp = fopen($fileDirHost.$fileDir.$fileSaveName,'x');
            fwrite($fp, $imageOutput);
            fclose($fp);

            array_push($bookData['bookImages'],array(
                'imageName'=>"Amazon Book Image",
                'imageUrl'=>$fileDir.$fileSaveName,
                'titleImage'=>true
            ));
            $titleImageDone = true;
        }

        $i=0;
        foreach($files as $file){
            if((($file->getSize())/1204)<=200){

                $fileName = substr($file->getClientOriginalName(),0,strpos($file->getClientOriginalName(), pathinfo($file->getClientOriginalName())['extension']));
                $fileSaveName = gmdate("Y-d-m_h_i_s_").rand(0,99999999).".".pathinfo($file->getClientOriginalName())['extension'];


                $file->move($fileDirHost.$fileDir, $fileSaveName);


                $bookImageArray=array();
                $bookImageArray['imageName'] = $fileName;
                $bookImageArray['imageUrl'] = $fileDir.$fileSaveName;


                if(array_key_exists('bookTitleImage',$bookData) && !$titleImageDone){

                    if($bookData['bookTitleImage']==null){
                        $bookImageArray['titleImage'] = false;

                    }elseif($i==$bookData['bookTitleImage']){
                        $bookImageArray['titleImage'] = true;

                    }else{
                        $bookImageArray['titleImage'] = false;
                    }
                }else{
                    $bookImageArray['titleImage'] = false;
                }

                array_push($bookData['bookImages'],$bookImageArray);

            }else{
                $fileUploadError = true;
            }
            $i++;
        }





        if(!$fileUploadError){


            if(array_key_exists('bookPublishDate',$bookData)){
                $publishDate = new \DateTime($bookData['bookPublishDate']);
                $bookData['bookPublishDate'] =$publishDate;
            }
            if(array_key_exists('bookAvailableDate',$bookData)){
                $availableDate = new \DateTime($bookData['bookAvailableDate']);
                $bookData['bookAvailableDate'] =$availableDate;
            }

            $bookData['bookSeller']=$userId;


            $bookForm = $this->createForm(new BookType(), $book);


            $bookForm->submit($bookData);

            if($bookForm->isValid()){
                $em->persist($book);
                $em->flush();
                return $this->_createJsonResponse('success',array('successTitle'=>"Book Successfully added to sell List"),200);
            }else{
                return $this->_createJsonResponse('error',array('errorTitle'=>"Could not add book","errorDescription"=>"Please check the form and submit again","errorData"=>$bookForm),200);

            }
        }else{
            return $this->_createJsonResponse('error',array('errorTitle'=>"Book was not Successfully Uploaded",'errorDescription'=>"Please select images less than or equal 200KB."),400);
        }

    }


    function _getBooksByKeywordAmazon($keyword, $page)
    {


        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemSearch";
        $amazonCredentials['params']["ItemPage"] = $page;
        $amazonCredentials['params']["Keywords"] = $keyword;
        $amazonCredentials['params']["SearchIndex"] = "Books";
        $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
        $getUrl = $this->_getUrlWithSignature($amazonCredentials);


        $xmlOutput = $this->get('api_caller')->call(new HttpGetHtml($getUrl, null, null));

        $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);


        /*$user = $this->container->get('security.token_storage')->getToken()->getUser();
        var_dump($user);*/


        $em = $this->getDoctrine()->getManager();
        $bookRepo = $em->getRepository("AppBundle:Book");
        $studentBooks=$bookRepo->getStudentBooksWithMultipleISBN($booksArray['books']);

        for($i = 0;$i<count($booksArray['books']);$i++){
            //Set Subtitle in Book
            if(strpos($booksArray['books'][$i]['bookTitle'],":")){
                $booksArray['books'][$i]['bookSubTitle']=substr($booksArray['books'][$i]['bookTitle'],strpos($booksArray['books'][$i]['bookTitle'],":")+2);
                $booksArray['books'][$i]['bookTitle'] = substr($booksArray['books'][$i]['bookTitle'],0,strpos($booksArray['books'][$i]['bookTitle'],":"));
            }

            //
            foreach($studentBooks as $studentBook){
                if(!strcmp(strval($studentBook['bookIsbn10']), strval($booksArray['books'][$i]['bookIsbn']))){
                    $booksArray['books'][$i]['bookPriceStudentLowest']="$".$studentBook['bookPriceSell'];
                    $booksArray['books'][$i]['bookPriceStudentLowestFound']=true;
                    break;
                }
            }
        }


        return $this->_createJsonResponse('success', array('successData'=>$booksArray),200);

    }

    function _getBooksByAsinAmazon($asin)
    {


        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemLookup";
        $amazonCredentials['params']["ItemId"] = $asin;
        $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
        $getUrl = $this->_getUrlWithSignature($amazonCredentials);
        $xmlOutput = $this->get('api_caller')->call(new HttpGetHtml($getUrl, null, null));

        $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);

        return $this->_createJsonResponse('success', array('successData'=>$booksArray),200);

    }

    function _getBooksByIsbnAmazon($isbn)
    {


        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemLookup";
        $amazonCredentials['params']["ItemId"] = $isbn;
        $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
        $amazonCredentials['params']["IdType"]="ISBN";
        $amazonCredentials['params']["SearchIndex"]="All";

        $getUrl = $this->_getUrlWithSignature($amazonCredentials);
//        var_dump($getUrl);
//        die();

        $xmlOutput = $this->get('api_caller')->call(new HttpGetHtml($getUrl, null, null));

        $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);

        return $this->_createJsonResponse('success', array('successData'=>$booksArray),200);

    }

    public function _addToCartAmazonUrl($bookOfferId){
        $amazonSearchParams = $this->_getAmazonSearchParams();
        $amazonSearchParams['params']['Operation'] = "CartCreate";
        $amazonSearchParams['params']['Item.1.OfferListingId'] = $bookOfferId;
        $amazonSearchParams['params']['Item.1.Quantity'] = "1";

        $cartUrl = $this->_getUrlWithSignature($amazonSearchParams);
        return $cartUrl;
    }

    public function _getBooksByIsbnCampusBooks($isbn)
    {
        $campusBooksApiInfo = $this->getParameter('campus_books_api_info');
        $apiKey = $campusBooksApiInfo['api_key'];
        $host = $campusBooksApiInfo['host'];
        $uri = $campusBooksApiInfo['uri'];

        $url= $host.$uri."?key=".$apiKey."&isbn=".$isbn."&format=json";

        $jsonOutput = $this->get('api_caller')->call(new HttpGetHtml($url, null, null));

        $arrayData= (json_decode($jsonOutput,true));

        return $this->_createJsonResponse('success',array('successData'=>$arrayData),200);

    }

    public function _getUrlWithSignature($amazonCredentials)
    {
        // sort the parameters
        ksort($amazonCredentials['params']);
        // create the canonicalization  query
        $canonicalizedQuery = array();
        foreach ($amazonCredentials['params'] as $param => $value) {
            $param = str_replace("%7E", "~", rawurlencode($param));
            $value = str_replace("%7E", "~", rawurlencode($value));
            $canonicalizedQuery[] = $param . "=" . $value;
        }
        $canonicalizedQuery = implode("&", $canonicalizedQuery);

        // create the string to sign
        $string_to_sign = $amazonCredentials['apiInfo']['method'] . "\n" . $amazonCredentials['apiInfo']['host'] . "\n" . $amazonCredentials['apiInfo']['uri'] . "\n" . $canonicalizedQuery;

        // calculate HMAC with SHA256 and base64-encoding
        $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $amazonCredentials['apiInfo']['privateKey'], true));

        // encode the signature for the request
        $signature = str_replace("%7E", "~", rawurlencode($signature));
        $url = "http://" . $amazonCredentials['apiInfo']['host'] . $amazonCredentials['apiInfo']['uri'] . "?" . $canonicalizedQuery . "&Signature=" . $signature;

        return $url;
    }

    public function _getAmazonSearchParams()
    {


        $amazonApiInfo = $this->getParameter('amazon_api_info');

        $apiInfo = array();
        $apiInfo['method'] = $amazonApiInfo['method'];
        $apiInfo['host'] = $amazonApiInfo['host'];
        $apiInfo['uri'] = $amazonApiInfo['uri'];
        $apiInfo['privateKey'] = $amazonApiInfo['private_key'];

//        $time = time();
//        $date = new  \DateTime();
//        $date->setTimestamp($time);

        $params = array();

        $params["AWSAccessKeyId"] = $amazonApiInfo['aws_access_key_id'];
        $params["AssociateTag"] = $amazonApiInfo['associate_tag'];
        $params["Service"] = "AWSECommerceService";
        $params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z");
        $params["Version"] = $amazonApiInfo['version'];
        $params["Power"] = "binding:hardcover or library or paperback";



        return array(
            'apiInfo' => $apiInfo,
            'params' => $params
        );

    }

    public function _parseMultipleBooksAmazonXmlResponse($xml)
    {

        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xml);

        $fileContents = trim(str_replace('"', "'", $fileContents));

        $simpleXml = simplexml_load_string($fileContents);

        $booksArray = array();
        foreach ($simpleXml->Items->Item as $item) {
            $booksArray[] = $this->_createJsonFromItemAmazon($item);
        }


        return array(
            'books' => $booksArray,
            'totalSearchResults' => (string)$simpleXml->Items->TotalResults
        );

    }

    public function _createJsonFromItemAmazon($item)
    {

        if (!empty($item->Offers->Offer->OfferListing->Price->FormattedPrice)) {
            $price = (string)$item->Offers->Offer->OfferListing->Price->FormattedPrice;
        } elseif (!empty($item->ListPrice->FormattedPrice)) {
            $price = (string)$item->ListPrice->FormattedPrice;
        } else {
            $price = "Not Found";
        }

        if (isset($item->ItemAttributes->Director)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Director;
        } elseif (isset($item->ItemAttributes->Author)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Author;
        } elseif (isset($item->ItemAttributes->Artist)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Artist;
        } else {
            $book_director_author_artist = 'No Author Found';
        }

        if(!empty($item->Offers->Offer->OfferListing->OfferListingId)){
            $offerId = (string)$item->Offers->Offer->OfferListing->OfferListingId;
        }else{
            $offerId = "";
        }


        if (!empty($item->MediumImage->URL)) {
            $book_image_medium_url = (string)$item->MediumImage->URL;
        } else {
            $book_image_medium_url = './images/misc/no_picture_100x125.jpg';
        }

        if (!empty($item->LargeImage->URL)) {
            $book_image_large_url = (string)$item->LargeImage->URL;
        } else {
            $book_image_large_url  = './images/misc/no_picture_100x125.jpg';
        }

        return array(
            'bookAsin' => (string)$item->ASIN,
            'bookTitle' => (string)$item->ItemAttributes->Title,
            'bookDirectorAuthorArtist' => $book_director_author_artist,
            'bookPriceAmazon' => $price,
            'bookIsbn' => (string)$item->ItemAttributes->ISBN,
            'bookEan' => (string)$item->ItemAttributes->EAN,
            'bookEdition' => (string)$item->ItemAttributes->Edition,
            'bookPublisher' => (string)$item->ItemAttributes->Publisher,
            'bookPublishDate' => (string)$item->ItemAttributes->PublicationDate,
            'bookBinding' => (string)$item->ItemAttributes->Binding,
            'bookImages'=>[
                array(
                    'image'=>$book_image_large_url,
                    'imageId'=>0
                )/*,
                array(
                    'image'=>$book_image_large_url,
                    'imageId'=>1
                ),
                array(
                    'image'=>$book_image_large_url,
                    'imageId'=>2
                ),
                array(
                    'image'=>$book_image_large_url,
                    'imageId'=>3
                )*/
            ],
            'bookDescription' => (string)$item->EditorialReviews->EditorialReview->Content,
            'bookPages' => (string)$item->ItemAttributes->NumberOfPages,
            'bookOfferId'=>$offerId,
            'bookLanguage'=> (string)$item->ItemAttributes->Languages->Language->Name,
        );
    }

    public function _createJsonResponse($key, $data,$code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
