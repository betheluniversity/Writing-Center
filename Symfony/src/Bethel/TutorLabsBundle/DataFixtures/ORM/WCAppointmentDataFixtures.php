<?php

namespace Bethel\TutorLabsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bethel\TutorLabsBundle\Entity\WCAppointmentData;

class WCAppointmentDataFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        //Time the appointment is scheduled to start.
        $StartTime = date_create('2014-09-18 18:05:00');
        echo date_format($StartTime, 'Y-m-d H:i:s');

        //Time the appointment is scheduled to end.
        $EndTime = date_create('2014-09-18 19:00:00');
        echo date_format($EndTime, 'Y-m-d H:i:s');

        //Time the appointment actually was completed.
        $CompletedTime = date_create('2014-09-18 19:00:00');
        echo date_format($CompletedTime, 'Y-m-d H:i:s');

        $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('ces55739');
        $testAppointment->setTutorUsername('mwe43644');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

        $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('mwe43644');
        $testAppointment->setTutorUsername('specou');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime($StartTime);
        $testAppointment->setCompletedTime($CompletedTime);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(3);
        $testAppointment->setStudentSignIn($StartTime);
        $testAppointment->setStudentSignOut($EndTime);
        $testAppointment->setAssignment('CWC Essay');
        $testAppointment->setComment('Searched for sources.');
        $testAppointment->setSuggestion('Fix grammatical errors.');
        $testAppointment->setMultilingual(true);
        $testAppointment->setCourseCode("COS371");
        $testAppointment->setCourseSection("1");
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

        $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('ces55739');
        $testAppointment->setTutorUsername('maf54223');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime($StartTime);
        $testAppointment->setCompletedTime($CompletedTime);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(3);
        $testAppointment->setStudentSignIn($StartTime);
        $testAppointment->setStudentSignOut($EndTime);
        $testAppointment->setAssignment('Christian Theo Essay');
        $testAppointment->setComment('Reviewed his rough draft.');
        $testAppointment->setSuggestion('Spend some more time researching Calvinism.');
        $testAppointment->setMultilingual(false);
        $testAppointment->setCourseCode("COS371");
        $testAppointment->setCourseSection("1");
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

        $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('ces55739');
        $testAppointment->setTutorUsername('mju85733');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime($StartTime);
        $testAppointment->setCompletedTime($CompletedTime);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(3);
        $testAppointment->setStudentSignIn($StartTime);
        $testAppointment->setStudentSignOut($EndTime);
        $testAppointment->setAssignment('ICA Essay');
        $testAppointment->setComment('We reviewed a sunoke way to format an essay.');
        $testAppointment->setSuggestion('Fix grammatical errors.');
        $testAppointment->setMultilingual(false);
        $testAppointment->setCourseCode("COS376");
        $testAppointment->setCourseSection("1");
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);


        //Time the appointment is scheduled to start.
        $StartTime = date_create('2014-08-07 10:30:00');
        echo date_format($StartTime, 'Y-m-d H:i:s');

        //Time the appointment is scheduled to end.
        $EndTime = date_create('2014-08-07 11:30:00');
        echo date_format($EndTime, 'Y-m-d H:i:s');

        //Time the appointment actually was completed.
        $CompletedTime = date_create('2014-08-07 11:30:00');
        echo date_format($CompletedTime, 'Y-m-d H:i:s');

        $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('ces55739');
        $testAppointment->setTutorUsername('centermanager');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(-1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

               $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('ces55739');
        $testAppointment->setTutorUsername('specou');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(-1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

               $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('mwe43644');
        $testAppointment->setTutorUsername('maf54223');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(-1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

        $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('mwe43644');
        $testAppointment->setTutorUsername('mju85733');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(-1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

                //Time the appointment is scheduled to start.
        $StartTime = date_create('2014-07-22 10:30:00');
        echo date_format($StartTime, 'Y-m-d H:i:s');

        //Time the appointment is scheduled to end.
        $EndTime = date_create('2014-07-22 11:30:00');
        echo date_format($EndTime, 'Y-m-d H:i:s');

        //Time the appointment actually was completed.
        $CompletedTime = date_create('2014-05-08 11:30:00');
        echo date_format($CompletedTime, 'Y-m-d H:i:s');

        $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('');
        $testAppointment->setTutorUsername('emh88758');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(-1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

               $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('');
        $testAppointment->setTutorUsername('specou');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(-1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

             $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('');
        $testAppointment->setTutorUsername('maf54223');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(-1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);

            $testAppointment = new WCAppointmentData();
        $testAppointment->setStudUsername('ces55739');
        $testAppointment->setTutorUsername('mju85733');
        $testAppointment->setProgram('CAS');
        $testAppointment->setStartTime($StartTime);
        $testAppointment->setEndTime($EndTime);
        $testAppointment->setActualStartTime(NULL);
        $testAppointment->setCompletedTime(NULL);
        $testAppointment->setProfEmail('testProf1@bethel.edu');
        $testAppointment->setRequestSub('No');
        $testAppointment->setCheckIn(-1);
        $testAppointment->setStudentSignIn(NULL);
        $testAppointment->setStudentSignOut(NULL);
        $testAppointment->setAssignment('');
        $testAppointment->setComment('');
        $testAppointment->setSuggestion('');
        $testAppointment->setMultilingual(false);
        $testAppointment->setCourseCode("COS371");
        $testAppointment->setDropInAppt(false);
        
        $manager->persist($testAppointment);





        
        $manager->flush();
    }
}