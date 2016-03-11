<?php
namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Book
 */
class Book
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
    private $bookTitle;

    /**
     * @var string
     *
     */
    private $bookDirectorAuthorArtist;

    /**
     * @var string
     *
     */
    private $bookEdition;

    /**
     * @var string
     *
     */
    private $bookIsbn10;

    /**
     * @var string
     *
     */
    private $bookIsbn13;

    /**
     * @var string
     *
     */
    private $bookPublisher;

    /**
     * @var string
     *
     */
    private $bookPublishDate;


    /**
     * @var string
     *
     */
    private $bookBinding;

    /**
     * @var string
     *
     */
    private $bookPage;

    /**
     * @var string
     *
     */
    private $bookPriceSell;

    /**
     * @var string
     *
     */
    private $bookLanguage;

    /**
     * @var string
     *
     */
    private $bookDescription;

    /**
     * @var string
     *
     */
    private $bookCondition;

    /**
     * @var string
     *
     */
    private $bookIsHighlighted;

    /**
     * @var string
     *
     */
    private $bookHasNotes;

    /**
     * @var string
     *
     */
    private $bookComment;

    /**
     * @var string
     *
     */
    private $bookContactMethod;

    /**
     * @var string
     *
     */
    private $bookContactHomeNumber;

    /**
     * @var string
     *
     */
    private $bookContactCellNumber;
    /**
     * @var string
     *
     */
    private $bookContactEmail;
    /**
     * @var string
     *
     */
    private $bookIsAvailablePublic;

    /**
     * @var string
     *
     */
    private $bookPaymentMethodCaShOnExchange;
    /**
     * @var string
     *
     */
    private $bookPaymentMethodCheck;

    /**
     * @var string
     *
     */
    private $bookTitleImageUrl;


    private $bookBuyer;

    private $bookSeller;

    private $messages;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set bookTitle
     *
     * @param string $bookTitle
     * @return Book
     */
    public function setBookTitle($bookTitle)
    {
        $this->bookTitle = $bookTitle;

        return $this;
    }

    /**
     * Get bookTitle
     *
     * @return string 
     */
    public function getBookTitle()
    {
        return $this->bookTitle;
    }

    /**
     * Set bookDirectorAuthorArtist
     *
     * @param string $bookDirectorAuthorArtist
     * @return Book
     */
    public function setBookDirectorAuthorArtist($bookDirectorAuthorArtist)
    {
        $this->bookDirectorAuthorArtist = $bookDirectorAuthorArtist;

        return $this;
    }

    /**
     * Get bookDirectorAuthorArtist
     *
     * @return string 
     */
    public function getBookDirectorAuthorArtist()
    {
        return $this->bookDirectorAuthorArtist;
    }

    /**
     * Set bookEdition
     *
     * @param string $bookEdition
     * @return Book
     */
    public function setBookEdition($bookEdition)
    {
        $this->bookEdition = $bookEdition;

        return $this;
    }

    /**
     * Get bookEdition
     *
     * @return string 
     */
    public function getBookEdition()
    {
        return $this->bookEdition;
    }

    /**
     * Set bookIsbn10
     *
     * @param string $bookIsbn10
     * @return Book
     */
    public function setBookIsbn10($bookIsbn10)
    {
        $this->bookIsbn10 = $bookIsbn10;

        return $this;
    }

    /**
     * Get bookIsbn10
     *
     * @return string 
     */
    public function getBookIsbn10()
    {
        return $this->bookIsbn10;
    }

    /**
     * Set bookIsbn13
     *
     * @param string $bookIsbn13
     * @return Book
     */
    public function setBookIsbn13($bookIsbn13)
    {
        $this->bookIsbn13 = $bookIsbn13;

        return $this;
    }

    /**
     * Get bookIsbn13
     *
     * @return string 
     */
    public function getBookIsbn13()
    {
        return $this->bookIsbn13;
    }

    /**
     * Set bookPublisher
     *
     * @param string $bookPublisher
     * @return Book
     */
    public function setBookPublisher($bookPublisher)
    {
        $this->bookPublisher = $bookPublisher;

        return $this;
    }

    /**
     * Get bookPublisher
     *
     * @return string 
     */
    public function getBookPublisher()
    {
        return $this->bookPublisher;
    }

    /**
     * Set bookPublishDate
     *
     * @param \DateTime $bookPublishDate
     * @return Book
     */
    public function setBookPublishDate($bookPublishDate)
    {
        $this->bookPublishDate = $bookPublishDate;

        return $this;
    }

    /**
     * Get bookPublishDate
     *
     * @return \DateTime 
     */
    public function getBookPublishDate()
    {
        return $this->bookPublishDate;
    }

    /**
     * Set bookBinding
     *
     * @param string $bookBinding
     * @return Book
     */
    public function setBookBinding($bookBinding)
    {
        $this->bookBinding = $bookBinding;

        return $this;
    }

    /**
     * Get bookBinding
     *
     * @return string 
     */
    public function getBookBinding()
    {
        return $this->bookBinding;
    }

    /**
     * Set bookPage
     *
     * @param string $bookPage
     * @return Book
     */
    public function setBookPage($bookPage)
    {
        $this->bookPage = $bookPage;

        return $this;
    }

    /**
     * Get bookPage
     *
     * @return string 
     */
    public function getBookPage()
    {
        return $this->bookPage;
    }



    /**
     * Set bookLanguage
     *
     * @param string $bookLanguage
     * @return Book
     */
    public function setBookLanguage($bookLanguage)
    {
        $this->bookLanguage = $bookLanguage;

        return $this;
    }

    /**
     * Get bookLanguage
     *
     * @return string 
     */
    public function getBookLanguage()
    {
        return $this->bookLanguage;
    }

    /**
     * Add messages
     *
     * @param \AppBundle\Entity\Message $messages
     * @return Book
     */
    public function addMessage(\AppBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \AppBundle\Entity\Message $messages
     */
    public function removeMessage(\AppBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set bookBuyer
     *
     * @param \AppBundle\Entity\User $bookBuyer
     * @return Book
     */
    public function setBookBuyer(\AppBundle\Entity\User $bookBuyer = null)
    {
        $this->bookBuyer = $bookBuyer;

        return $this;
    }

    /**
     * Get bookBuyer
     *
     * @return \AppBundle\Entity\User 
     */
    public function getBookBuyer()
    {
        return $this->bookBuyer;
    }

    /**
     * Set bookSeller
     *
     * @param \AppBundle\Entity\User $bookSeller
     * @return Book
     */
    public function setBookSeller(\AppBundle\Entity\User $bookSeller = null)
    {
        $this->bookSeller = $bookSeller;

        return $this;
    }

    /**
     * Get bookSeller
     *
     * @return \AppBundle\Entity\User 
     */
    public function getBookSeller()
    {
        return $this->bookSeller;
    }

    /**
     * Set bookPriceSell
     *
     * @param string $bookPriceSell
     * @return Book
     */
    public function setBookPriceSell($bookPriceSell)
    {
        $this->bookPriceSell = $bookPriceSell;

        return $this;
    }

    /**
     * Get bookPriceSell
     *
     * @return string 
     */
    public function getBookPriceSell()
    {
        return $this->bookPriceSell;
    }
}
