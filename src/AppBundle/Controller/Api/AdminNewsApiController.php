<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookImage;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Contact;
use AppBundle\Entity\News;
use AppBundle\Entity\Quote;
use AppBundle\Form\Type\BookDealType;
use AppBundle\Form\Type\ContactType;
use AppBundle\Form\Type\NewsType;
use AppBundle\Form\Type\QuoteType;
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

class AdminNewsApiController extends Controller
{


    /**
     * Get News for Admin
     */
    public function getNewsAction(Request $request){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $newsRepo=$em->getRepository('AppBundle:News');

            $pageSize = $data["pageSize"];
            $searchQuery = filter_var($data["searchQuery"], FILTER_SANITIZE_STRING);
            $pageNumber = $data["pageNumber"];
            $sort = $data["sort"];

            $totalNumber = $newsRepo->getAllNewsSearchNumber($searchQuery);
            $searchResults= $newsRepo->getAllNewsSearchResult($searchQuery, $pageNumber, $pageSize,$sort);

            $newsData = array();
            foreach($searchResults as $news){
                $news['newsDateTime']=$news['newsDateTime']->format('d M Y');
                $images = $newsRepo->findOneById($news['newsId'])->getNewsImages();

                $news['newsImages']=array();
                foreach($images as $image){
                    array_push($news['newsImages'], array(
                        'imageId'=>$image->getId(),
                        'image'=>$image->getNewsImageUrl()
                    ));
                }

                array_push($newsData,$news);
            }


            $data = array(
                'totalNews' => $newsData ,
                'totalNumber' => $totalNumber
            );

            return $this->_createJsonResponse('success', array('successData'=>array('news'=>$data)), 200);
        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }


    /**
     * Update News
     */
    public function updateNewsAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->getContent();
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();
            $newsRepo=$em->getRepository('AppBundle:News');

            $news = $newsRepo->findOneById($data['newsId']);

            if($news!=null){
                $newsForm = $this->createForm(new NewsType(), $news);
                $newsForm->remove('newsImages');
                $data['newsDateTime']=gmdate('Y-m-d H:i:s');
                $newsForm->submit($data);

                if ($newsForm->isValid()) {
                    $em->persist($news);
                    $em->flush();
                    return $this->_createJsonResponse('success', array(
                        'successTitle' => "News has been updated"
                    ), 200);
                } else {
                    return $this->_createJsonResponse('error', array("errorTitle"=>"Could Not update news","errorData" => $newsForm), 400);
                }
            }else{
                return $this->_createJsonResponse('error', array("errorTitle"=>"News was not found"), 400);
            }


        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }

    /**
     * Add News
     */
    public function addNewsAction(Request $request){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if(in_array('ROLE_ADMIN_USER',$user->getRoles(),true)){

            $content = $request->get('news');
            $data = json_decode($content, true);
            $em = $this->getDoctrine()->getManager();

            //Prepare File
            $fileDirHost = $this->container->getParameter('kernel.root_dir');
            $fileDir = '/../web/newsImages/';
            $fileNameDir = '/newsImages/';
            $files = $request->files;

            //Return Error if image not found
            if(count($files)==0){
                return $this->_createJsonResponse('error', array('errorTitle' => "Cannot Add News", 'errorDescription' => "Image not Found"), 400);
            }

            //Upload Image
            $fileUploadError = false;
            $data['newsImages']=array();
            foreach ($files as $file) {
                if ((($file->getSize()) / 1024) <= 200) {
                    $fileSaveName = gmdate("Y-d-m_h_i_s_") . rand(0, 99999999) . "." . 'jpg';
                    $file->move($fileDirHost . $fileDir, $fileSaveName);

                    $this->_smart_resize_image($fileDirHost.$fileDir.$fileSaveName , null, 0 , 0 , false , $fileDirHost.$fileDir.$fileSaveName , false , false ,100 );

                    array_push($data['newsImages'],array(
                        'newsImageUrl'=>$fileNameDir . $fileSaveName
                    ));
                } else {
                    $fileUploadError = true;
                }
            }
            //If Error Occurs than Return Error Message
            if($fileUploadError)return $this->_createJsonResponse('error', array('errorTitle' => "Cannot Add Quote", 'errorDescription' => "Image is more than 200 KB"), 400);




            $news = new News();

            $data['newsStatus']='Activated';
            $data['newsDateTime']=gmdate('Y-m-d H:i:s');


            $newsForm = $this->createForm(new NewsType(), $news);

            $newsForm->submit($data);

            if ($newsForm->isValid()) {
                $em->persist($news);
                $em->flush();

                $images = $news->getNewsImages();
                $imageData=array();
                foreach($images as $image){
                    array_push($imageData,array(
                        'imageId'=>$image->getId(),
                        'image'=>$image->getNewsImageUrl()
                    ));
                }

                return $this->_createJsonResponse('success', array(
                    'successTitle' => "News has been created",
                    'successData'=>array(
                        'newsId'=>$news->getId(),
                        'newsTitle'=>$news->getNewsTitle(),
                        'newsDescription'=>$news->getNewsDescription(),
                        'newsDateTime'=>$news->getNewsDateTime()->format('d M Y'),
                        'newsStatus'=>$news->getNewsStatus(),
                        'newsImages'=>$imageData
                    )
                ), 201);

            } else {
                return $this->_createJsonResponse('error', array("errorTitle"=>"Could Not create news","errorData" => $newsForm), 400);
            }


        }else{
            return $this->_createJsonResponse('error', array('errorTitle'=>"You are not authorized to see this page."), 400);
        }
    }


    //Image Resize Function
    function _smart_resize_image($file,
                                $string             = null,
                                $width              = 0,
                                $height             = 0,
                                $proportional       = false,
                                $output             = 'file',
                                $delete_original    = true,
                                $use_linux_commands = false,
                                $quality = 100
    ) {

        if ( $height <= 0 && $width <= 0 ) return false;
        if ( $file === null && $string === null ) return false;
        # Setting defaults and meta
        $info                         = $file !== null ? getimagesize($file) : getimagesizefromstring($string);
        $image                        = '';
        $final_width                  = 0;
        $final_height                 = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;
        # Calculating proportionality
        if ($proportional) {
            if      ($width  == 0)  $factor = $height/$height_old;
            elseif  ($height == 0)  $factor = $width/$width_old;
            else                    $factor = min( $width / $width_old, $height / $height_old );
            $final_width  = round( $width_old * $factor );
            $final_height = round( $height_old * $factor );
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }
        # Loading image to memory according to type
        switch ( $info[2] ) {
            case IMAGETYPE_JPEG:  $file !== null ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
            case IMAGETYPE_GIF:   $file !== null ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
            case IMAGETYPE_PNG:   $file !== null ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
            default: return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);
            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color  = imagecolorsforindex($image, $transparency);
                $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            }
            elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


        # Taking care of original, if needed
        if ( $delete_original ) {
            if ( $use_linux_commands ) exec('rm '.$file);
            else @unlink($file);
        }
        # Preparing a method of providing result
        switch ( strtolower($output) ) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination and image quality
        switch ( $info[2] ) {
            case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
            case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9*$quality)/10.0);
                imagepng($image_resized, $output, $quality);
                break;
            default: return false;
        }
        return true;
    }

    public function _createJsonResponse($key, $data, $code)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize([$key => $data], 'json');
        $response = new Response($json, $code);
        return $response;
    }


}
