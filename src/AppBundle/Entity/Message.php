<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Book
 */
class Message
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
    private $messageBody;

    /**
     * @var datetime
     *
     */
    private $messageDateTime;


    private $user;


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
     * Set messageBody
     *
     * @param string $messageBody
     * @return Message
     */
    public function setMessageBody($messageBody)
    {
        $this->messageBody = $messageBody;

        return $this;
    }

    /**
     * Get messageBody
     *
     * @return string 
     */
    public function getMessageBody()
    {
        return $this->messageBody;
    }

    /**
     * Set messageDateTime
     *
     * @param \DateTime $messageDateTime
     * @return Message
     */
    public function setMessageDateTime($messageDateTime)
    {
        $this->messageDateTime = $messageDateTime;

        return $this;
    }

    /**
     * Get messageDateTime
     *
     * @return \DateTime 
     */
    public function getMessageDateTime()
    {
        return $this->messageDateTime;
    }

    /**
     * Set book
     *
     * @param \AppBundle\Entity\Book $book
     * @return Message
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

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Message
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
