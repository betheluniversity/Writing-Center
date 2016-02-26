<?php

namespace Bethel\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Groups;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\EntityListeners({"Bethel\UserBundle\EventListener\UserListener"})
 * @ORM\Entity(repositoryClass="Bethel\UserBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"sessionDetails"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Bethel\TutorLabsBundle\Entity\WCStudentBans", mappedBy="user")
     * @Groups({"sessionDetails"})
     *
     */
    private $bans;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="Bethel\TutorLabsBundle\Entity\WCEmailPreferences", cascade={"persist", "remove" })
     * @ORM\JoinColumn(name="email_pref_id", referencedColumnName="id")
     */
    private $email_pref;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     * @Groups({"sessionDetails"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Groups({"sessionDetails"})
     *
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Groups({"sessionDetails"})
     *
     */
    private $lastName;

    /**
     * @var boolean
     */
    private $multilingual;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @Groups({"sessionDetails"})
     *
     */
    private $roles;

    /**
     * @var boolean
     */
    private $enabled;

    public function __construct() {
        $this->bans = new ArrayCollection();
        $this->roles = new ArrayCollection();
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
     * @param mixed $bans
     * @return User
     */
    public function setBans($bans)
    {
        $this->bans = $bans;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBans()
    {
        return $this->bans;
    }

    /**
     * Set email_pref
     *
     * @param \Bethel\TutorLabsBundle\Entity\WCEmailPreferences $emailPref
     * @return User
     */
    public function setEmailPref(\Bethel\TutorLabsBundle\Entity\WCEmailPreferences $emailPref = null)
    {
        $this->email_pref = $emailPref;
    
        return $this;
    }

    /**
     * Get email_pref
     *
     * @return \Bethel\TutorLabsBundle\Entity\WCEmailPreferences 
     */
    public function getEmailPref()
    {
        return $this->email_pref;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function setEnabled($boolean)
    {
        $this->enabled = (Boolean) $boolean;

        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set multilingual
     *
     * @param boolean $multilingual
     * @return User
     */
    public function setMultilingual($multilingual)
    {
        $this->multilingual = $multilingual;
    
        return $this;
    }

    /**
     * Get multilingual
     *
     * @return boolean 
     */
    public function getMultilingual()
    {
        return $this->multilingual;
    }

    // UserInterface related methods

    // The only requirement is that the class implements UserInterface.
    // The methods in this interface should therefore be defined in the custom user class:
    // getRoles(), getPassword(), getSalt(), getUsername(), eraseCredentials()
    // http://symfony.com/doc/current/cookbook/security/entity_provider.html

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function addRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            // updating the inverse side
            $role->addUser($this);
        }

        return $this;
        /*$role = strtoupper($role);
        //if ($role === static::ROLE_DEFAULT) {
        //    return $this;
        //}

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;*/
    }

    // For now this project is using only single roles so we need a method that
    // replaces all of a users' roles.
    public function setRoles(Role $role){
        $currentRoles = $this->getRoles();
        foreach($currentRoles as $currentRole){
            $this->removeRole($currentRole);
        }
        $this->addRole($role);
        return $this;
    }

    /**
     * Never use this to check if this user has access to anything!
     *
     * Use the SecurityContext, or an implementation of AccessDecisionManager
     * instead, e.g.
     *
     *         $securityContext->isGranted('ROLE_USER');
     *
     * @param string $role
     *
     * @return boolean
     */
    public function hasRole($role)
    {
        $userRoles = $this->getRoles();
        /** @var $userRole \Bethel\EntityBundle\Entity\Role */
        foreach($userRoles as $userRole) {
            if($userRole->getRole() == $role) {
                return true;
            }
        }

        return false;
    }

    /*public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }*/

    public function removeRole($role)
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
            // updating the inverse side
            $role->removeUser($this);
        }

        return $this;
    }

    public function getSalt()
    {
        // We should not need a salt while using CAS as a provider
        return null;
    }

    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}