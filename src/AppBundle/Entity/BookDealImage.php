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
    private $imageUrl;


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
    public function __toString()
    {
        return strval($this->id);
    }
}
