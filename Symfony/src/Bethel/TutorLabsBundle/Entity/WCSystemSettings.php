<?php

namespace Bethel\TutorLabsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="WCSystemSettings")
 */
class WCSystemSettings
{
  /**
   * @ORM\Id
   * @ORM\Column(name="ID", type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(name="apptLimit", type="integer")
   * @Groups({"displaySettings"})
   */
  protected $apptLimit;

  /**
   * @ORM\Column(name="timeLimit", type="integer")
   * @Groups({"displaySettings"})
   */
  protected $timeLimit;

  /**
   * @ORM\Column(name="banLimit", type="integer")
   * @Groups({"displaySettings"})
   */
  protected $banLimit;

    /**
   * @ORM\Column(name="qualtricsLink", type="text")
   * @Groups({"displaySettings"})
   */
  protected $qualtricsLink;



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
     * Set apptLimit
     *
     * @param integer $apptLimit
     * @return CompletedAppointment
     */
    public function setApptLimit($apptLimit)
    {
        $this->apptLimit = $apptLimit;

        return $this;
    }

    /**
     * Get apptLimit
     *
     * @return string
     */
    public function getApptLimit()
    {
        return $this->apptLimit;
    }

    /**
     * Set timeLimit
     *
     * @param integer $timeLimit
     * @return CompletedAppointment
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    /**
     * Get timeLimit
     *
     * @return integer
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

      /**
     * Set banLimit
     *
     * @param integer $banLimit
     * @return CompletedAppointment
     */
    public function setBanLimit($banLimit)
    {
        $this->banLimit = $banLimit;

        return $this;
    }

    /**
     * Get banLimit
     *
     * @return integer
     */
    public function getBanLimit()
    {
        return $this->banLimit;
    }

    /**
     * Set qualtricsLink
     *
     * @param text $qualtricsLink
     * @return CompletedAppointment
     */
    public function setQualtricsLink($qualtricsLink)
    {
        $this->qualtricsLink = $qualtricsLink;

        return $this;
    }

    /**
     * Get qualtricsLink
     *
     * @return text
     */
    public function getQualtricsLink()
    {
        return $this->qualtricsLink;
    }
}