<?php

namespace Bethel\TutorLabsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="WCStudentBans")
 */
class WCStudentBans{
    /**
    * @ORM\Id
    * @ORM\Column(name="ID", type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    * @Groups({"displayBans"})
    */
    protected $id;

    /**
    * @var integer
    *
    * @ORM\ManyToOne(targetEntity="Bethel\UserBundle\Entity\User", inversedBy="bans")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
    */
    private $user;

    /**
    * @ORM\Column(name="bannedDate", type="datetime")
    * @Groups({"displayBans"})
    */
    protected $bannedDate;


    /**
    * @ORM\Column(name="Username", type="string")
    * @Groups({"displayBans"})
    */
    protected $username;

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
    * Set Username
    *
    * @param string $username
    * @return CompletedAppointment
    */
    public function setUsername($username)
    {
    $this->username = $username;

    return $this;
    }

    /**
    * Get Username
    *
    * @return string
    */
    public function getUsername()
    {
    return $this->username;
    }


    /**
    * Set bannedDate
    *
    * @param \datetime $bannedDate
    * @return bannedDate
    */
    public function setBannedDate(\datetime $bannedDate)
    {
    $this->bannedDate = $bannedDate;

    return $this;
    }

    /**
    * Get bannedDate
    *
    * @return \datetime
    */
    public function getBannedDate()
    {
    return $this->bannedDate;
    }

    /**
    * Get bannedDate
    *
    * @return string
    */
    public function getStringBannedDate()
    {
    return $this->bannedDate->format('Y-m-d'); // H:i:s');
    }

    /**
    * Set user
    *
    * @param \Bethel\UserBundle\Entity\User $user
    * @return WCStudentBans
    */
    public function setUser(\Bethel\UserBundle\Entity\User $user = null)
    {
    $this->user = $user;

    return $this;
    }

    /**
    * Get user
    *
    * @return \Bethel\UserBundle\Entity\User
    */
    public function getUser()
    {
    return $this->user;
    }
}