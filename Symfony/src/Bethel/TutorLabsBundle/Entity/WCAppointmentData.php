<?php

namespace Bethel\TutorLabsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="WCAppointmentData")
 */
class WCAppointmentData
{
    /**
     * @ORM\Id
     * @ORM\Column(name="ID", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"displayAppointments"})
     */
    protected $id;



    /**
     * @ORM\Column(name="StudUsername", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $StudUsername;



    /**
     * @ORM\Column(name="TutorUsername", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $TutorUsername;


    /**
     * @ORM\Column(name="Program", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $Program;



    /**
     * @ORM\Column(name="StartTime", type="datetime")
     * @Groups({"displayAppointments"})
     */
    protected $StartTime;

    /**
     * @ORM\Column(name="EndTime", type="datetime")
     * @Groups({"displayAppointments"})
     */
    protected $EndTime;

    /**
     * @ORM\Column(name="ActualStartTime", type="datetime", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $ActualStartTime;

    /**
     * @ORM\Column(name="CompletedTime", type="datetime", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $CompletedTime;

    //Check In Values
    // -1 - Appointment has been made and nothing has happened since
    // 0 - Tutor Signs in and Student has not showed up
    // 1 - Tutor and Student are signed in
    // 2 - Student has Signed out.
//Final values    //Final values
    // 3 - Normal appointment.
    // 4 - No Show
    // 5 - Walk In
    /**
    * @ORM\Column(name="CheckIn", type="integer")
    * @Groups({"displayAppointments"})
    */
    protected $CheckIn;

    /**
     * @ORM\Column(name="StudentSignIn", type="datetime", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $StudentSignIn;

    /**
     * @ORM\Column(name="StudentSignOut", type="datetime", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $StudentSignOut;

    /**
     * @ORM\Column(name="ProfEmail", type="string", nullable=true)
     */
    protected $ProfEmail;

    /**
     * @ORM\Column(name="RequestSub", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $RequestSub;

    /**
     * @ORM\Column(name="Assignment", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $Assignment;

    /**
     * @ORM\Column(name="Notes", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $Comment;

    /**
     * @ORM\Column(name="Suggestions", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $Suggestion;

    /**
    * @ORM\Column(name="multilingual", type="boolean")
    * @Groups({"displayAppointments"})
    */
    protected $multilingual;

    /**
     * @ORM\Column(name="CourseCode", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $CourseCode;

    /**
     * @ORM\Column(name="ProfUsername", type="string", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $ProfUsername;

    /**
     * @ORM\Column(name="CourseSection", type="integer", nullable=true)
     * @Groups({"displayAppointments"})
     */
    protected $CourseSection;

    /**
    * @ORM\Column(name="DropInAppt", type="boolean")
    * @Groups({"displayAppointments"})
    */
    protected $DropInAppt;



    public function __construct()
    {
        $this->multilingual = false;
        $this->Suggestion = false;
        $this->Assignment = false;
        $this->Comment = false;
        $this->Program = "CAS";
        $this->RequestSub = false;
        $this->DropInAppt = false;
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
     * @return AppointmentData
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set StudUsername
     *
     * @param string $studUsername
     * @return AppointmentData
     */
    public function setStudUsername($studUsername)
    {
        $this->StudUsername = $studUsername;

        return $this;
    }

    /**
     * Get StudUsername
     *
     * @return string
     */
    public function getStudUsername()
    {
        return $this->StudUsername;
    }

    /**
     * Set TutorUsername
     *
     * @param string $tutorUsername
     * @return WCAppointmentData
     */
    public function setTutorUsername($tutorUsername)
    {
        $this->TutorUsername = $tutorUsername;

        return $this;
    }

    /**
     * Get TutorUsername
     *
     * @return string
     */
    public function getTutorUsername()
    {
        return $this->TutorUsername;
    }

    /**
     * Set Program
     *
     * @param string $program
     * @return WCAppointmentData
     */
    public function setProgram($program)
    {
        $this->Program = $program;

        return $this;
    }

    /**
     * Get Program
     *
     * @return string
     */
    public function getProgram()
    {
        return $this->Program;
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

    /**
     * Set ActualStartTime
     *
     * @param \datetime $ActualStartTime
     * @return WCAppointmentData
     */
    public function setActualStartTime(\datetime $ActualStartTime=null)
    {


        if ($ActualStartTime)
            $this->ActualStartTime = $ActualStartTime;
        return $this;
    }

    /**
     * Get ActualStartTime
     *
     * @return \datetime
     */
    public function getActualStartTime()
    {
        return $this->ActualStartTime;
    }

    /**
     * Set CompletedTime
     *
     * @param \datetime $completedTime
     * @return WCAppointmentData
     */
    public function setCompletedTime(\datetime $completedTime=null)
    {


        if ($completedTime)
            $this->CompletedTime = $completedTime;
        return $this;
    }

    /**
     * Get CompletedTime
     *
     * @return \datetime
     */
    public function getCompletedTime()
    {
        return $this->CompletedTime;
    }
    
    /**
    * Set Check In
    *
    * @return WCAppointmentData
    */
    public function setCheckIn($checkIn)
    {
        $this->CheckIn = $checkIn;
        
        return $this;
    }
    /**
    * Get CheckIn
    *
    * @return integer
    */
    public function getCheckIn()
    {
        return $this->CheckIn;   
    }

    /**
     * Set StudentSignIn
     *
     * @param \datetime $studentSignIn
     * @return WCAppointmentData
     */
    public function setStudentSignIn(\datetime $studentSignIn=null)
    {
        $this->StudentSignIn = $studentSignIn;

        return $this;
    }

    /**
     * Get StudentSignIn
     *
     * @return \datetime
     */
    public function getStudentSignIn()
    {
        return $this->StudentSignIn;
    }

    /**
     * Set StudentSignOut
     *
     * @param \datetime $studentSignOut
     * @return WCAppointmentData
     */
    public function setStudentSignOut(\datetime $studentSignOut=null)
    {
        $this->StudentSignOut = $studentSignOut;

        return $this;
    }

    /**
     * Get StudentSignOut
     *
     * @return \datetime
     */
    public function getStudentSignOut()
    {
        return $this->StudentSignOut;
    }

    /**
     * Set ProfEmail
     *
     * @param string $profEmail
     * @return WCAppointmentData
     */
    public function setProfEmail($profEmail)
    {
        $this->ProfEmail = $profEmail;

        return $this;
    }

    /**
     * Get ProfEmail
     *
     * @return string
     */
    public function getProfEmail()
    {
        return $this->ProfEmail;
    }

    /**
     * Set RequestSub
     *
     * @param string $RequestSub
     * @return WCAppointmentData
     */
    public function setRequestSub($RequestSub)
    {
        $this->RequestSub = $RequestSub;

        return $this;
    }

    /**
     * Get RequestSub
     *
     * @return string
     */
    public function getRequestSub()
    {
        return $this->RequestSub;
    }

    /**
     * Set Assignment
     *
     * @param string $Assignment
     * @return WCAppointmentData
     */
    public function setAssignment($Assignment)
    {
        $this->Assignment = $Assignment;

        return $this;
    }

    /**
     * Get Assignment
     *
     * @return string
     */
    public function getAssignment()
    {
        return $this->Assignment;
    }

    /**
     * Set Comment
     *
     * @param string $Comment
     * @return WCAppointmentData
     */
    public function setComment($Comment)
    {
        $this->Comment = $Comment;

        return $this;
    }

    /**
     * Get Comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->Comment;
    }

    /**
     * Set Suggestion
     *
     * @param string $Suggestion
     * @return WCAppointmentData
     */
    public function setSuggestion($Suggestion)
    {
        $this->Suggestion = $Suggestion;

        return $this;
    }

    /**
     * Get Suggestion
     *
     * @return string
     */
    public function getSuggestion()
    {
        return $this->Suggestion;
    }

        /**
     * Set Multilingual
     *
     * @param string $multilingual
     * @return WCAppointmentData
     */
    public function setMultilingual($multilingual)
    {
        $this->multilingual = $multilingual;

        return $this;
    }

    /**
     * Get Multilingual
     *
     * @return string
     */
    public function getMultilingual()
    {
        return $this->multilingual;
    }

        /**
     * Set CourseCode
     *
     * @param string $CourseCode
     * @return WCAppointmentData
     */
    public function setCourseCode($CourseCode)
    {
        $this->CourseCode = $CourseCode;

        return $this;
    }

    /**
     * Get CourseCode
     *
     * @return string
     */
    public function getCourseCode()
    {
        return $this->CourseCode;
    }        

    /**
     * Set ProfUsername
     *
     * @param string $ProfUsername
     * @return WCAppointmentData
     */
    public function setProfUsername($ProfUsername)
    {
        $this->ProfUsername = $ProfUsername;

        return $this;
    }

    /**
     * Get ProfUsername
     *
     * @return string
     */
    public function getProfUsername()
    {
        return $this->ProfUsername;
    }        

    /**
     * Set CourseSection
     *
     * @param integer $CourseSection
     * @return WCAppointmentData
     */
    public function setCourseSection($CourseSection)
    {
        $this->CourseSection = $CourseSection;

        return $this;
    }

    /**
     * Get CourseSection
     *
     * @return integer
     */
    public function getCourseSection()
    {
        return $this->CourseSection;
    }


    /**
     * Set DropInAppt
     *
     * @param booleon $DropInAppt
     * @return WCAppointmentData
     */
    public function setDropInAppt($DropInAppt)
    {
        $this->DropInAppt = $DropInAppt;

        return $this;
    }

    /**
     * Get DropInAppt
     *
     * @return string
     */
    public function getDropInAppt()
    {
        return $this->DropInAppt;
    }
}