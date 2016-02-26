<?php

namespace Bethel\TutorLabsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WCEmailPreferences
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class WCEmailPreferences
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="SubRequestEmail", type="boolean")
     */
    private $subRequestEmail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="StudentSignUpEmail", type="boolean")
     */
    private $studentSignUpEmail;

    public function __construct()
    {
        $this->subRequestEmail = true;
        $this->studentSignUpEmail = true;
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
     * Set id
     *
     * @param int $id
     * @return id
     */
    public function setID($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Set subRequestEmail
     *
     * @param boolean $subRequestEmail
     * @return EmailPreferences
     */
    public function setSubRequestEmail($subRequestEmail)
    {
        $this->subRequestEmail = $subRequestEmail;
    
        return $this;
    }

    /**
     * Get subRequestEmail
     *
     * @return boolean 
     */
    public function getSubRequestEmail()
    {
        return $this->subRequestEmail;
    }

    /**
     * Set studentSignUpEmail
     *
     * @param boolean $studentSignUpEmail
     * @return EmailPreferences
     */
    public function setStudentSignUpEmail($studentSignUpEmail)
    {
        $this->studentSignUpEmail = $studentSignUpEmail;
    
        return $this;
    }

    /**
     * Get studentSignUpEmail
     *
     * @return boolean 
     */
    public function getStudentSignUpEmail()
    {
        return $this->studentSignUpEmail;
    }

}