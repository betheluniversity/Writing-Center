<?php

namespace Bethel\TutorLabsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="WCSchedule")
 */
class WCSchedule
{
	/**
	 * @ORM\Id
	 * @ORM\Column(name="ID", type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;


	/**
	 * @ORM\Column(name="timeStart", type="datetime")
	 */
	protected $timeStart;

	

	/**
	 * @ORM\Column(name="timeEnd", type="datetime")
	 */
	protected $timeEnd;

	

	/**
	 * @ORM\Column(name="isActive", type="string")
	 */
	protected $isActive;

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
     * Set timeStart
     *
     * @param \datetime $timeStart
     * @return WCSchedule
     */
    public function setTimeStart(\datetime $timeStart)
    {
        $this->timeStart = $timeStart;
    
        return $this;
    }

    /**
     * Get timeStart
     *
     * @return \datetime 
     */
    public function getTimeStart()
    {
        return $this->timeStart;
    }

    /**
     * Set timeEnd
     *
     * @param \datetime $timeEnd
     * @return WCSchedule
     */
    public function setTimeEnd(\datetime $timeEnd)
    {
        $this->timeEnd = $timeEnd;
    
        return $this;
    }

    /**
     * Get timeEnd
     *
     * @return \datetime
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }


    /**
     * Set isActive
     *
     * @param string $isActive
     * @return WCSchedule
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return string 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

}