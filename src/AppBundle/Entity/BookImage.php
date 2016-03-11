<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * BookImage
 */
class BookImage
{


    /**
     * @var integer
     *
     */
    protected $id;

    /**
     * @var string
     *
     */
    private $imageName;

    /**
     * @var string
     *
     */
    private $imageUrl;

    /**
     * @var boolean
     *
     */
    private $titleImage;


    private $book;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set imageName
     *
     * @param string $imageName
     * @return BookImage
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return string 
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Set imageUrl
     *
     * @param string $imageUrl
     * @return BookImage
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get imageUrl
     *
     * @return string 
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set titleImage
     *
     * @param boolean $titleImage
     * @return BookImage
     */
    public function setTitleImage($titleImage)
    {
        $this->titleImage = $titleImage;

        return $this;
    }

    /**
     * Get titleImage
     *
     * @return boolean 
     */
    public function getTitleImage()
    {
        return $this->titleImage;
    }

    /**
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return BookImage
     */
    public function setBook(\AppBundle\Entity\Book $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \AppBundle\Entity\Book 
     */
    public function getBook()
    {
        return $this->book;
    }
}
