<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookDeal;
use AppBundle\Entity\BookImage;
use AppBundle\Entity\Campus;
use AppBundle\Form\Type\BookDealType;
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
     * Search By Keyword Amazon Api (Normal Call)
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
     * Search By Keyword Amazon Api (Api Call)
     */
    public function searchByKeywordAmazonApiWithApiCallAction(Request $request)
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
     * Get Lowest Online Price Campus Books Api
     */
    public function getLowestPriceByIsbnCampusBooksApiAction(Request $request)
    {
        $isbn = $request->query->get('isbn');
        if ($isbn != null) {
            $lowestOnlinePrice = $this->_getBooksLowestPriceByIsbnCampusBooks($isbn);
            return $this->_createJsonResponse('success', array('successData' => array('bookPriceOnlineLowest' => $lowestOnlinePrice)), 200);
        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "Invalid Isbn"), 400);
        }

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
            $bookOfferId = "";
        }

        $addToCartAmazonUrl = $this->_addToCartAmazonUrl($bookOfferId);
        $xmlOutput = $this->get('api_caller')->call(new HttpGetHtml($addToCartAmazonUrl, null, null));


        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xmlOutput);

        $fileContents = trim(str_replace('"', "'", $fileContents));

        $simpleXml = simplexml_load_string($fileContents);


        return $this->_createJsonResponse('success', array('successData' => array('cartUrl' => (string)$simpleXml->Cart->PurchaseURL)), 200);

    }


    /**
     * Get Campus Deals by ISBN Api Call with AccessToken
     */
    public function getCampusDealsByIsbnApiAction(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);

        $campusId = $this->get('security.token_storage')->getToken()->getUser()->getCampus()->getId();

        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');

        if ($data != null) {
            if (array_key_exists('isbn', $data)) {
                $deals = array(
                    'buyerToSeller' => array(),
                    'sellerToBuyer' => array()
                );
                $onCampusDeals = $bookDealRepo->getCampusDealsByIsbn($data['isbn'], $campusId);
                //Increase View Counter
                if(count($onCampusDeals)>0){
                    $bookDealRepo->increaseBookViewCounter($onCampusDeals);

                    foreach ($onCampusDeals as $deal) {

                        //Formatting Date
                        if ($deal['bookAvailableDate'] != null) {
                            $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
                        }

                        //dividing via Contact Method
                        if (strpos('buyerToSeller', $deal['bookContactMethod']) !== false) {
                            array_push($deals['buyerToSeller'], $deal);
                        } else {
                            array_push($deals['sellerToBuyer'], $deal);
                        }

                    }

                }




                return $this->_createJsonResponse('success', array('successData' => $deals), 200);

            } else {
                return $this->_createJsonResponse('error', array('errorTitle' => "Wrong Data Provided", 'errorDescription' => "Please Provide Isbn"), 400);
            }
        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "Wrong Data Provided", 'errorDescription' => "Please Provide Isbn"), 400);
        }

    }


    /**
     * Get Campus Deals by ISBN
     */
    public function getCampusDealsByIsbnAction(Request $request)
    {

        $content = $request->getContent();
        $data = json_decode($content, true);

        $em = $this->getDoctrine()->getManager();
        $bookDealRepo = $em->getRepository('AppBundle:BookDeal');

        if ($data != null) {
            if (array_key_exists('isbn', $data) && array_key_exists('campusId', $data)) {
                $deals = array(
                    'buyerToSeller' => array(),
                    'sellerToBuyer' => array()
                );
                $onCampusDeals = $bookDealRepo->getCampusDealsByIsbn($data['isbn'], $data['campusId']);

                //Increase View Counter
                if(count($onCampusDeals)>0){
                    $bookDealRepo->increaseBookViewCounter($onCampusDeals);

                    foreach ($onCampusDeals as $deal) {

                        //Formatting Date
                        if ($deal['bookAvailableDate'] != null) {
                            $deal['bookAvailableDate'] = $deal['bookAvailableDate']->format('d M Y');
                        }

                        //dividing via Contact Method
                        if (strpos('buyerToSeller', $deal['bookContactMethod']) !== false) {
                            array_push($deals['buyerToSeller'], $deal);
                        } else {
                            array_push($deals['sellerToBuyer'], $deal);
                        }

                    }
                }



                return $this->_createJsonResponse('success', array('successData' => $deals), 200);

            } else {
                return $this->_createJsonResponse('error', array('errorTitle' => "Wrong Data Provided", 'errorDescription' => "Please Provide Isbn"), 400);
            }
        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "Wrong Data Provided", 'errorDescription' => "Please Provide Isbn"), 400);
        }


    }


    /**
     * Sell New Book
     */
    public function addNewSellBookAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        //Get Image Save Dir
        $fileDirHost = $this->container->getParameter('kernel.root_dir');
        //TODO Fix that below directory
        $fileDir = '/../web/bookImages/';
        $fileNameDir = '/bookImages/';

        //GET Request Data
        $content = $request->get('data');
        $data = json_decode($content, true);
        $bookData = $data['bookData'];
        $bookDealData = $data['bookDealData'];

        // Image Files
        $files = $request->files;
        $bookDealData['bookDealImages'] = array();



        //Upload All Deal Images
        $fileUploadError = false;
        foreach ($files as $file) {
            if ((($file->getSize()) / 1024) <= 200) {
                $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . "." . pathinfo($file->getClientOriginalName())['extension'];
                $file->move($fileDirHost . $fileDir, $fileSaveName);
                $bookImageArray = array();
                $bookImageArray['imageUrl'] = $fileNameDir . $fileSaveName;
                array_push($bookDealData['bookDealImages'], $bookImageArray);
            } else {
                $fileUploadError = true;
            }
        }
        //If Error Occurs than Return Error Message
        if($fileUploadError)return $this->_createJsonResponse('error', array('errorTitle' => "Cannot Add Sell Book", 'errorDescription' => "Some Files are more than 200 KB"), 400);



        if(array_key_exists('bookType',$bookData)){

            /* Inserting Book Info */

            //Check if Book Already Exists in DB

            $alreadyExistedBook = $this->_checkIfBookExistInDatabase($bookData['bookIsbn10']);


            $bookId = null;

            if (!($alreadyExistedBook instanceof Book)) {
                // Check if Its a custom book or got from amazon
                if(!strcmp('newSellBook',$bookData['bookType'])){

                    //Insert Book Image from amazon
                    $imageOutput = $this->get('api_caller')->call(new HttpGetHtml($bookData['bookImage'], null, null));
                    $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".png";
                    $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
                    fwrite($fp, $imageOutput);
                    fclose($fp);
                    $bookData['bookImage'] = $fileNameDir . $fileSaveName;


                }elseif(!strcmp('newSellCustomBook',$bookData['bookType'])){

                    //Insert First Image from files
                    $bookData['bookImage'] = $bookDealData['bookDealImages'][0]['imageUrl'];

                }


                //Insert New Book
                $book = new Book();
                $bookForm = $this->createForm(new BookType(), $book);

                $bookData['bookPublishDate'] = (new \DateTime($bookData['bookPublishDate']))->format("Y-m-d");

                if(array_key_exists('bookDescription',$bookData)){
                    $bookData['bookDescription'] = strip_tags($bookData['bookDescription']);
                }

                if(array_key_exists('bookSubTitle',$bookData)){
                    $bookData['bookTitle']=$bookData['bookTitle'].": ".$bookData['bookSubTitle'];
                }
                //TODO Insert Book Amazon Price
                $bookForm->submit($bookData);

                if ($bookForm->isValid()) {
                    $em->persist($book);
                    $em->flush();
                    $bookId = $book->getId();
                } else {
                    return $this->_createJsonResponse('error', array("errorData" => $bookForm), 400);
                }

            } else {
                $bookId = $alreadyExistedBook->getId();
            }

            /* Inserting Book Deal */


            $bookDeal = new BookDeal();
            $bookDealForm = $this->createForm(new BookDealType(), $bookDeal);

            $date = new \DateTime($bookDealData['bookAvailableDate']);
            $bookDealData['bookAvailableDate'] = $date->format("Y-m-d");
            $bookDealData['seller'] = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
            $bookDealData['bookSellingStatus'] = "Selling";
            $bookDealData['bookStatus'] = "Activated";
            $bookDealData['bookViewCount'] = 0;
            $bookDealData['book'] = $bookId;
            //Set Email on Book Deal
            if(!array_key_exists('bookContactEmail',$bookDealData)){
                $bookDealData['bookContactEmail'] = $this->container->get('security.token_storage')->getToken()->getUser()->getEmail();
            }


            $bookDealForm->submit($bookDealData);

            if ($bookDealForm->isValid()) {
                $em->persist($bookDeal);
                $em->flush();
                return $this->_createJsonResponse('success', array("successTitle" => "Book has been inserted into you selling List"), 200);
            } else {
                return $this->_createJsonResponse('error', array("errorData" => $bookDealForm), 400);

            }


        }else{
            return $this->_createJsonResponse('error', array("errorTitle" => "Could not add book on sell page","errorDescription" => "Check the form and submit again"), 400);
        }

    }


    /**
     * Sell New Custom Book
     */
    public function _addNewCustomSellBookAction(Request $request){

    }


    function _checkIfBookExistInDatabase($isbn10)
    {
        $em = $this->getDoctrine()->getManager();
        $bookRepo = $em->getRepository('AppBundle:Book');
        $book = $bookRepo->findOneBy(array('bookIsbn10' => $isbn10));
        if ($book) {
            return $book;
        } else {
            return false;
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


        if (count($booksArray['books']) > 0) {
            $em = $this->getDoctrine()->getManager();
            $bookDealRepo = $em->getRepository("AppBundle:BookDeal");
            $user = $this->container->get('security.context')->getToken()->getUser();
            $campusId = null;
            if ($user != "anon.") {
                $campusId = $user->getCampus()->getId();
            }
            $studentBooks = $bookDealRepo->getStudentBooksWithMultipleISBN($booksArray['books'], $campusId);

            for ($i = 0; $i < count($booksArray['books']); $i++) {
                //Set Subtitle in Book
                /*If Logged In then Find Lowest Campus Price of Just users university
                    Otherwise if not logged in then find entire database and find the lowest price in whole student2student System
                */
                if (strpos($booksArray['books'][$i]['bookTitle'], ":")) {
                    $booksArray['books'][$i]['bookSubTitle'] = substr($booksArray['books'][$i]['bookTitle'], strpos($booksArray['books'][$i]['bookTitle'], ":") + 2);
                    $booksArray['books'][$i]['bookTitle'] = substr($booksArray['books'][$i]['bookTitle'], 0, strpos($booksArray['books'][$i]['bookTitle'], ":"));
                }

                // Getting Campus Lowest Price
                foreach ($studentBooks as $studentBook) {
                    if (!strcmp(strval($studentBook['bookIsbn10']), strval($booksArray['books'][$i]['bookIsbn']))) {
                        $booksArray['books'][$i]['bookPriceStudentLowest'] = "$" . $studentBook['bookPriceSell'];
                        $booksArray['books'][$i]['bookPriceStudentLowestFound'] = true;
                        break;
                    }
                }

                foreach ($booksArray['books'] as $book) {
                    if (!array_key_exists('bookPriceStudentLowestFound', $book)) {
                        $booksArray['books'][array_search($book, $booksArray['books'])]['bookPriceStudentLowestFound'] = false;
                    }
                }
            }


            return $this->_createJsonResponse('success', array('successData' => $booksArray), 200);
        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "No Books were found", "errorDescription" => "Please Refine your search query and try again."), 400);
        }


    }

    function _getBooksByAsinAmazon($asin)
    {
        $em = $this->getDoctrine()->getManager();
        $bookRepo = $em->getRepository("AppBundle:Book");
        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemLookup";
        $amazonCredentials['params']["ItemId"] = $asin;
        $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
        $getUrl = $this->_getUrlWithSignature($amazonCredentials);
        $xmlOutput = $this->get('api_caller')->call(new HttpGetHtml($getUrl, null, null));

        $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);

        if(count($booksArray['books'])>0){
            //Insert Book INTo DB
            $insertedBookId = $this->_insertBookIntoDatabase($booksArray['books'][0]);

            $images = array();
            if($insertedBookId){
                $bookImages = $bookRepo->getBookAndDealImages($insertedBookId);
                $insertedBook=$bookRepo->findOneById($insertedBookId);
                //GET FIRST IMAGE OF THAT BOOK
                array_push($images,array(
                    'image'=>$insertedBook->getBookImage(),
                    'imageId'=>0
                ));
            }


            //GET All IMAGES OF THAT BOOK's DEALS


            for($i=0;$i<count($bookImages);$i++){
                array_push($images,array(
                    'image'=>$bookImages[$i]['imageUrl'],
                    'imageId'=>($i+1)
                ));
            }
            $booksArray['books'][0]['bookImages'] = $images;
            $booksArray['books'][0]['bookId'] = $insertedBookId;
            $booksArray['books'][0]['bookDescription'] = strip_tags($booksArray['books'][0]['bookDescription']);

            //DONE 1.Insert Book into DB
            //DONE 2.GET All Images Of That Book With Deals & Add with Response
            //DONE 3.Return the DB response. Not the Amazon response
            //DONE 4.Increase View Number of Each Deal related to that Book (Do it on Second call)

            for ($i = 0; $i < count($booksArray['books']); $i++) {
                //Fixing Title & Sub Title
                if (strpos($booksArray['books'][$i]['bookTitle'], ":")) {
                    $booksArray['books'][$i]['bookSubTitle'] = substr($booksArray['books'][$i]['bookTitle'], strpos($booksArray['books'][$i]['bookTitle'], ":") + 2);
                    $booksArray['books'][$i]['bookTitle'] = substr($booksArray['books'][$i]['bookTitle'], 0, strpos($booksArray['books'][$i]['bookTitle'], ":"));
                }
                //Fixing Date
                if ($booksArray['books'][$i]['bookPublishDate'] != null) {
                    $booksArray['books'][$i]['bookPublishDate'] = (new \DateTime($booksArray['books'][$i]['bookPublishDate']))->format('d M Y');
                }

            }

        }


        if (count($booksArray['books']) > 0) {
            return $this->_createJsonResponse('success', array('successData' => $booksArray), 200);
        } else {
            return $this->_createJsonResponse('error', array('errorTitle' => "No Book was found", "errorDescription" => "Please provide real ASIN Number"), 400);
        }


    }

    function _insertBookIntoDatabase($book){

        $em = $this->getDoctrine()->getManager();
        $alreadyExistedBook = $this->_checkIfBookExistInDatabase($book['bookIsbn']);

        // Check if Book is already in DB
        if (!($alreadyExistedBook instanceof Book)) {

            //Insert Book Image from amazon
            $fileDirHost = $this->container->getParameter('kernel.root_dir');
            $fileDir = '/../web/bookImages/';
            $fileNameDir = '/bookImages/';

            $imageOutput = $this->get('api_caller')->call(new HttpGetHtml($book['bookImages'][0]['image'], null, null));
            $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . ".png";
            $fp = fopen($fileDirHost . $fileDir . $fileSaveName, 'x');
            fwrite($fp, $imageOutput);
            fclose($fp);

            $book['bookImage'] = $fileNameDir . $fileSaveName;

            //Insert New Book
            $bookEntity = new Book();
            $bookForm = $this->createForm(new BookType(), $bookEntity);

            $book['bookIsbn10']=$book['bookIsbn'];
            $book['bookIsbn13']=$book['bookEan'];
            $book['bookPublishDate'] = (new \DateTime($book['bookPublishDate']))->format("Y-m-d");
            $book['bookPage'] = $book['bookPages'];
            $book['bookDescription'] = strip_tags($book['bookDescription']);
            $book['bookAmazonPrice'] = substr($book['bookPriceAmazon'],1);

            $bookForm->submit($book);

            if ($bookForm->isValid()) {
                $em->persist($bookEntity);
                $em->flush();
                return $bookEntity->getId();
            } else {
                return false;
            }

        }else{
            return $alreadyExistedBook->getId();
        }
    }

    function _getBooksByIsbnAmazon($isbn)
    {


        $amazonCredentials = $this->_getAmazonSearchParams();

        $amazonCredentials['params']['Operation'] = "ItemLookup";
        $amazonCredentials['params']["ItemId"] = $isbn;
        $amazonCredentials['params']["ResponseGroup"] = "Medium,Offers";
        $amazonCredentials['params']["IdType"] = "ISBN";
        $amazonCredentials['params']["SearchIndex"] = "All";

        $getUrl = $this->_getUrlWithSignature($amazonCredentials);


        $xmlOutput = $this->get('api_caller')->call(new HttpGetHtml($getUrl, null, null));

        $booksArray = $this->_parseMultipleBooksAmazonXmlResponse($xmlOutput);

        $newBookArray = array();
        foreach ($booksArray['books'] as $book) {
            if (strcmp($book['bookBinding'], "Kindle Edition") && strcmp($book['bookPriceAmazon'], "Not Found")) {

                //Title Subtitle Formatting
                if (strpos($book['bookTitle'], ":")) {
                    $book['bookSubTitle'] = substr($book['bookTitle'], strpos($book['bookTitle'], ":") + 2);
                    $book['bookTitle'] = substr($book['bookTitle'], 0, strpos($book['bookTitle'], ":"));
                }
                array_push($newBookArray, $book);
            }
        }
        $booksArray['books'] = $newBookArray;

        return $this->_createJsonResponse('success', array('successData' => $booksArray), 200);

    }

    public function _addToCartAmazonUrl($bookOfferId)
    {
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

        $url = $host . $uri . "?key=" . $apiKey . "&isbn=" . $isbn . "&format=json";

        $jsonOutput = $this->get('api_caller')->call(new HttpGetHtml($url, null, null));

        $arrayData = (json_decode($jsonOutput, true));

        if (array_key_exists('status', $arrayData['response'])) {
            if (!strpos($arrayData['response']['status'], 'error')) {
                return $this->_createJsonResponse('error', array('errorTitle' => "No Book was found", "errorDescription" => "Please provide real ISBN Number"), 400);
            }
        } else {
            return $this->_createJsonResponse('success', array('successData' => $arrayData), 200);
        }


    }

    public function _getBooksLowestPriceByIsbnCampusBooks($isbn)
    {
        $campusBooksApiInfo = $this->getParameter('campus_books_api_info_lowest_price');
        $apiKey = $campusBooksApiInfo['api_key'];
        $host = $campusBooksApiInfo['host'];
        $uri = $campusBooksApiInfo['uri'];

        $url = $host . $uri . "?key=" . $apiKey . "&isbn=" . $isbn;

        $xmlOutput = $this->get('api_caller')->call(new HttpGetHtml($url, null, null));


        $fileContents = str_replace(array("\n", "\r", "\t"), '', $xmlOutput);

        $simpleXml = simplexml_load_string($fileContents);


        $priceArray = array();
        foreach ($simpleXml->page->offers->condition as $condition) {

            foreach ($condition->offer as $offer) {
                array_push($priceArray, (floatval($offer->total_price[0])));
            }

        }

        return "$" . min($priceArray);


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

        //Getting Price
        if (!empty($item->Offers->Offer->OfferListing->Price->FormattedPrice)) {
            $price = (string)$item->Offers->Offer->OfferListing->Price->FormattedPrice;
        } elseif (!empty($item->ListPrice->FormattedPrice)) {
            $price = (string)$item->ListPrice->FormattedPrice;
        } else {
            $price = "Not Found";
        }

        //Getting Author
        if (isset($item->ItemAttributes->Director)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Director;
        } elseif (isset($item->ItemAttributes->Author)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Author;
        } elseif (isset($item->ItemAttributes->Artist)) {
            $book_director_author_artist = (string)$item->ItemAttributes->Artist;
        } else {
            $book_director_author_artist = 'No Author Found';
        }

        //Getting offer
        if (!empty($item->Offers->Offer->OfferListing->OfferListingId)) {
            $offerId = (string)$item->Offers->Offer->OfferListing->OfferListingId;
        } else {
            $offerId = "";
        }


        //Getting Image
        if (!empty($item->MediumImage->URL)) {
            $book_image_medium_url = (string)$item->MediumImage->URL;
        } else {
            $book_image_medium_url = './images/misc/no_picture_100x125.jpg';
        }

        if (!empty($item->LargeImage->URL)) {
            $book_image_large_url = (string)$item->LargeImage->URL;
        } else {
            $book_image_large_url = './images/misc/no_picture_100x125.jpg';
        }

        //Getting Description
        if (!empty($item->EditorialReviews->EditorialReview->Content)) {
            $description = (string)$item->EditorialReviews->EditorialReview->Content;
        } else {
            $description = '';
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
            'bookImages' => [
                array(
                    'image' => $book_image_large_url,
                    'imageId' => 0
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
            'bookDescription' => $description,
            'bookPages' => (string)$item->ItemAttributes->NumberOfPages,
            'bookOfferId' => $offerId,
//            'bookLanguage'=> (string)$item->ItemAttributes->Languages->Language->Name,
        );
    }

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }

}
