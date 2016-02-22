<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Campus;
use AppBundle\Entity\Referral;
/**
 * User
 */
class User extends BaseUser
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     *
     */
    private $fullName;


    /**
     * @var string
     *
     */
    private $googleId;

    /**
     * @var string
     *
     */
    private $googleEmail;

    /**
     * @var string
     *
     */
    private $googleToken;

    /**
     * @var string
     *
     */
    private $facebookId;

    /**
     * @var string
     *
     */
    private $facebookEmail;


    /**
     * @var string
     *
     */
    private $facebookToken;

    /**
     * @var string
     *
     */
    private $registrationStatus;



    private $referral;

    private $campus;


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
     * Set fullName
     *
     * @param string $fullName
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set googleId
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * Get googleId
     *
     * @return string 
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Set facebookId
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get facebookId
     *
     * @return string 
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }


    /**
     * Set referral
     *
     * @param Referral $referral
     * @return User
     */
    public function setReferral(Referral $referral = null)
    {
        $this->referral = $referral;

        return $this;
    }

    /**
     * Get referral
     *
     * @return Referral
     */
    public function getReferral()
    {
        return $this->referral;
    }

    /**
     * Set campus
     *
     * @param Campus $campus
     * @return User
     */
    public function setCampus(Campus $campus = null)
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * Get campus
     *
     * @return Campus
     */
    public function getCampus()
    {
        return $this->campus;
    }

    

    /**
     * Set registrationStatus
     *
     * @param string $registrationStatus
     * @return User
     */
    public function setRegistrationStatus($registrationStatus)
    {
        $this->registrationStatus = $registrationStatus;

        return $this;
    }

    /**
     * Get registrationStatus
     *
     * @return string 
     */
    public function getRegistrationStatus()
    {
        return $this->registrationStatus;
    }

    /**
     * Set googleEmail
     *
     * @param string $googleEmail
     * @return User
     */
    public function setGoogleEmail($googleEmail)
    {
        $this->googleEmail = $googleEmail;

        return $this;
    }

    /**
     * Get googleEmail
     *
     * @return string 
     */
    public function getGoogleEmail()
    {
        return $this->googleEmail;
    }

    /**
     * Set googleToken
     *
     * @param string $googleToken
     * @return User
     */
    public function setGoogleToken($googleToken)
    {
        $this->googleToken = $googleToken;

        return $this;
    }

    /**
     * Get googleToken
     *
     * @return string 
     */
    public function getGoogleToken()
    {
        return $this->googleToken;
    }

    /**
     * Set facebookEmail
     *
     * @param string $facebookEmail
     * @return User
     */
    public function setFacebookEmail($facebookEmail)
    {
        $this->facebookEmail = $facebookEmail;

        return $this;
    }

    /**
     * Get facebookEmail
     *
     * @return string 
     */
    public function getFacebookEmail()
    {
        return $this->facebookEmail;
    }

    /**
     * Set facebookToken
     *
     * @param string $facebookToken
     * @return User
     */
    public function setFacebookToken($facebookToken)
    {
        $this->facebookToken = $facebookToken;

        return $this;
    }

    /**
     * Get facebookToken
     *
     * @return string 
     */
    public function getFacebookToken()
    {
        return $this->facebookToken;
    }

    /**
     * Get facebookToken
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt) {
        $this->salt = $salt;
        return $this;
    }
}
