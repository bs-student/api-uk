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
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->bookImages = new ArrayCollection();
    }

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
     * @var date
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
     * @var text
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
     * @var boolean
     *
     */
    private $bookPaymentMethodCaShOnExchange;
    /**
     * @var boolean
     *
     */
    private $bookPaymentMethodCheque;
    /**
     * @var date
     *
     */
    private $bookAvailableDate;




    private $bookImages;

    private $bookBuyer;

    private $bookSeller;

    private $messages;





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

    /**
     * Set bookDescription
     *
     * @param string $bookDescription
     * @return Book
     */
    public function setBookDescription($bookDescription)
    {
        $this->bookDescription = $bookDescription;

        return $this;
    }

    /**
     * Get bookDescription
     *
     * @return string 
     */
    public function getBookDescription()
    {
        return $this->bookDescription;
    }

    /**
     * Set bookCondition
     *
     * @param string $bookCondition
     * @return Book
     */
    public function setBookCondition($bookCondition)
    {
        $this->bookCondition = $bookCondition;

        return $this;
    }

    /**
     * Get bookCondition
     *
     * @return string 
     */
    public function getBookCondition()
    {
        return $this->bookCondition;
    }

    /**
     * Set bookIsHighlighted
     *
     * @param string $bookIsHighlighted
     * @return Book
     */
    public function setBookIsHighlighted($bookIsHighlighted)
    {
        $this->bookIsHighlighted = $bookIsHighlighted;

        return $this;
    }

    /**
     * Get bookIsHighlighted
     *
     * @return string 
     */
    public function getBookIsHighlighted()
    {
        return $this->bookIsHighlighted;
    }

    /**
     * Set bookHasNotes
     *
     * @param string $bookHasNotes
     * @return Book
     */
    public function setBookHasNotes($bookHasNotes)
    {
        $this->bookHasNotes = $bookHasNotes;

        return $this;
    }

    /**
     * Get bookHasNotes
     *
     * @return string 
     */
    public function getBookHasNotes()
    {
        return $this->bookHasNotes;
    }

    /**
     * Set bookComment
     *
     * @param string $bookComment
     * @return Book
     */
    public function setBookComment($bookComment)
    {
        $this->bookComment = $bookComment;

        return $this;
    }

    /**
     * Get bookComment
     *
     * @return string 
     */
    public function getBookComment()
    {
        return $this->bookComment;
    }

    /**
     * Set bookContactMethod
     *
     * @param string $bookContactMethod
     * @return Book
     */
    public function setBookContactMethod($bookContactMethod)
    {
        $this->bookContactMethod = $bookContactMethod;

        return $this;
    }

    /**
     * Get bookContactMethod
     *
     * @return string 
     */
    public function getBookContactMethod()
    {
        return $this->bookContactMethod;
    }

    /**
     * Set bookContactHomeNumber
     *
     * @param string $bookContactHomeNumber
     * @return Book
     */
    public function setBookContactHomeNumber($bookContactHomeNumber)
    {
        $this->bookContactHomeNumber = $bookContactHomeNumber;

        return $this;
    }

    /**
     * Get bookContactHomeNumber
     *
     * @return string 
     */
    public function getBookContactHomeNumber()
    {
        return $this->bookContactHomeNumber;
    }

    /**
     * Set bookContactCellNumber
     *
     * @param string $bookContactCellNumber
     * @return Book
     */
    public function setBookContactCellNumber($bookContactCellNumber)
    {
        $this->bookContactCellNumber = $bookContactCellNumber;

        return $this;
    }

    /**
     * Get bookContactCellNumber
     *
     * @return string 
     */
    public function getBookContactCellNumber()
    {
        return $this->bookContactCellNumber;
    }

    /**
     * Set bookContactEmail
     *
     * @param string $bookContactEmail
     * @return Book
     */
    public function setBookContactEmail($bookContactEmail)
    {
        $this->bookContactEmail = $bookContactEmail;

        return $this;
    }

    /**
     * Get bookContactEmail
     *
     * @return string 
     */
    public function getBookContactEmail()
    {
        return $this->bookContactEmail;
    }

    /**
     * Set bookIsAvailablePublic
     *
     * @param string $bookIsAvailablePublic
     * @return Book
     */
    public function setBookIsAvailablePublic($bookIsAvailablePublic)
    {
        $this->bookIsAvailablePublic = $bookIsAvailablePublic;

        return $this;
    }

    /**
     * Get bookIsAvailablePublic
     *
     * @return string 
     */
    public function getBookIsAvailablePublic()
    {
        return $this->bookIsAvailablePublic;
    }

    /**
     * Set bookPaymentMethodCaShOnExchange
     *
     * @param string $bookPaymentMethodCaShOnExchange
     * @return Book
     */
    public function setBookPaymentMethodCaShOnExchange($bookPaymentMethodCaShOnExchange)
    {
        $this->bookPaymentMethodCaShOnExchange = $bookPaymentMethodCaShOnExchange;

        return $this;
    }

    /**
     * Get bookPaymentMethodCaShOnExchange
     *
     * @return string 
     */
    public function getBookPaymentMethodCaShOnExchange()
    {
        return $this->bookPaymentMethodCaShOnExchange;
    }

    /**
     * Set bookPaymentMethodCheck
     *
     * @param string $bookPaymentMethodCheck
     * @return Book
     */
    public function setBookPaymentMethodCheck($bookPaymentMethodCheck)
    {
        $this->bookPaymentMethodCheck = $bookPaymentMethodCheck;

        return $this;
    }

    /**
     * Get bookPaymentMethodCheck
     *
     * @return string 
     */
    public function getBookPaymentMethodCheck()
    {
        return $this->bookPaymentMethodCheck;
    }

    /**
     * Add bookImages
     *
     * @param \AppBundle\Entity\BookImage $bookImages
     * @return Book
     */
    public function addBookImage(\AppBundle\Entity\BookImage $bookImages)
    {
        $this->bookImages->add($bookImages);
        $bookImages->setBook($this);
        return $this;
    }

    /**
     * Remove bookImages
     *
     * @param \AppBundle\Entity\BookImage $bookImages
     */
    public function removeBookImage(\AppBundle\Entity\BookImage $bookImages)
    {
        $this->bookImages->removeElement($bookImages);
    }

    /**
     * Get bookImages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBookImages()
    {
        return $this->bookImages;
    }

    /**
     * Set bookPaymentMethodCheque
     *
     * @param boolean $bookPaymentMethodCheque
     * @return Book
     */
    public function setBookPaymentMethodCheque($bookPaymentMethodCheque)
    {
        $this->bookPaymentMethodCheque = $bookPaymentMethodCheque;

        return $this;
    }

    /**
     * Get bookPaymentMethodCheque
     *
     * @return boolean 
     */
    public function getBookPaymentMethodCheque()
    {
        return $this->bookPaymentMethodCheque;
    }

    /**
     * Set bookAvailableDate
     *
     * @param \DateTime $bookAvailableDate
     * @return Book
     */
    public function setBookAvailableDate($bookAvailableDate)
    {
        $this->bookAvailableDate = $bookAvailableDate;

        return $this;
    }

    /**
     * Get bookAvailableDate
     *
     * @return \DateTime 
     */
    public function getBookAvailableDate()
    {
        return $this->bookAvailableDate;

    }

    public function __toString()
    {
        return strval($this->id);
    }


}
