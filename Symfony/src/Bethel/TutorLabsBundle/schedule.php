<?php
// src/Blogger/BlogBundle/Entity/Enquiry.php
//testing
namespace Bethel\TutorLabsBundle\Entity;

class Schedule
{
    protected $studentType;

    protected $scheduleBy;

    protected $time;

    protected $tutor;

    public function getStudentType()
    {
        return $this->studentType;
    }

    public function setStudentType($studentType)
    {
        $this->studentType = $studentType;
    }

    public function getScheduleBy()
    {
        return $this->scheduleBy;
    }

    public function setScheduleBy($email)
    {
        $this->scheduleBy = $scheduleBy;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function getTutor()
    {
        return $this->tutor;
    }

    public function setTutor($tutor)
    {
        $this->tutor = $tutor;
    }
}