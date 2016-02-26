<?php

namespace Bethel\TutorLabsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="WCDropInAppointments")
 */
class WCDropInAppointments
{
  /**
   * @ORM\Id
   * @ORM\Column(name="ID", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

   /**
     * @ORM\Column(name="StartTime", type="datetime")
     */
    protected $StartTime;

    /**
     * @ORM\Column(name="EndTime", type="datetime")
     */
    protected $EndTime;

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
     * Get StartTime
     *
     * @return \datetime
     */
    public function getStartTime()
    {
        return $this->StartTime;
    }

    /**
     * Set StartTime
     *
     * @param \datetime $startTime
     * @return WCAppointmentData
     */
    public function setStartTime(\datetime $startTime)
    {
        $this->StartTime = $startTime;

        return $this;
    }

    /**
     * Set EndTime
     *
     * @param \datetime $endTime
     * @return WCAppointmentData
     */
    public function setEndTime(\datetime $endTime)
    {
        $this->EndTime = $endTime;

        return $this;
    }

    /**
     * Get EndTime
     *
     * @return \datetime
     */
    public function getEndTime()
    {
        return $this->EndTime;
    }
}