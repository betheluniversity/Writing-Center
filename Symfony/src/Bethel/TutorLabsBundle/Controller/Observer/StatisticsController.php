<?php

namespace Bethel\TutorLabsBundle\Controller\Observer;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use DatePeriod;
use DateTime;
use DateInterval;


class StatisticsController extends Controller{

    public function statisticsViewAction(){
        /*
            This is the default view for the view statistics action. Just return the template for now.
        */
        return $this->render('BethelTutorLabsBundle:Observer:observer_statistics.html.twig');
    }

    public function statisticsCallAction(){
    //  1st     Get the start/end dates and get the checkbox values.
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        $startdate=$request->request->get('startdate', '');
        $enddate=$request->request->get('enddate', '');

        $viewAll=$request->request->get('viewAll', '');
        $multilingualRadio=$request->request->get('multilingual', '');
        $totalVisits=$request->request->get('totalVisits', '');
        $busiestTimeOfDay=$request->request->get('busiestTimeOfDay', '');
        $busiestDay=$request->request->get('busiestDay', '');
        $busiestTutor=$request->request->get('busiestTutor', '');
        $noShows=$request->request->get('noShows', '');
        $busiestWeek=$request->request->get('busiestWeek', '');
        $byCourse=$request->request->get('byCourse', '');
        $getStudentEmail=$request->request->get('getStudentEmail', '');

    //  2nd     Query for all appointments within a date range.

        $multilingual = true;
        if($multilingualRadio == 'multilingual')
            $multilingual = true;
        elseif($multilingualRadio == 'normal')
            $multilingual = false;



        //get all appointments.
        $repository = $helper->getWCAppointmentRepository();
        if($multilingualRadio == 'all'){
            $query = $repository->createQueryBuilder('p')
                ->where("p.StartTime >= :start AND p.StartTime <= :end AND (p.CheckIn LIKE :NormalAppt OR p.CheckIn LIKE :WalkInAppt)")
                ->setParameters(array('start' => date_create($startdate), 'end' => date_create($enddate), 'NormalAppt' => '3', 'WalkInAppt' => '5')) //3 means normal appointment
                ->getQuery();
        }
        else{ // if normal only or multilingual only
            $query = $repository->createQueryBuilder('p')
                ->where("p.StartTime >= :start AND p.StartTime <= :end AND p.multilingual = :multilingual AND (p.CheckIn LIKE :NormalAppt OR p.CheckIn LIKE :WalkInAppt)")
                ->setParameters(array('start' => date_create($startdate), 'end' => date_create($enddate), 'multilingual' => $multilingual, 'NormalAppt' => '3', 'WalkInAppt' => '5')) //3 means normal appointment
                ->getQuery();
        }

        $appointments = $query->getResult();

    //  3rd     Only use certain attributes based upon the checkboxes given.
        $totalVisitsArray = array();
        $busiestTimeOfDayArray = array();
        $busiestDayArray = array();
        $busiestTutorArray = array();
        $getBusiestTutorArray = array();
        $busiestWeekArray = array();
        $byCourseArray = array();
        $noShowsArray = 0;
        $getStudentEmailArray = array();

        if($viewAll == 'true'){
            $totalVisits = true;
            $busiestTimeOfDay = true;
            $busiestDay = true;
            $busiestTutor = true;
            $noShows = true;
            $busiestWeek = true;
            $byCourse = true;
            $getStudentEmail = true;
        }
        if($totalVisits == true)
            $totalVisitsArray = $this->getTotalVisits($appointments);
        if($busiestTimeOfDay == true)
            $busiestTimeOfDayArray = $this->getBusiestTimeOfDay($appointments);
        if($busiestDay == true)
            $busiestDayArray = $this->getBusiestDay($appointments);
        if($busiestTutor == true)
            $busiestTutorArray = $this->getBusiestTutor($appointments);
        if($noShows == true)
            $noShowsArray = $this->getNoShows($multilingual, $multilingualRadio, $startdate, $enddate);
        if($busiestWeek == true)
            $busiestWeekArray = $this->getBusiestWeek($appointments, $startdate, $enddate);
        if($byCourse == true)
            $byCourseArray = $this->getByCourse($appointments, $startdate, $enddate);
        if($getStudentEmail == true)
           $getStudentEmailArray = $this->getStudentEmail($appointments);


        //  4th     Then return the data in a nice set of arrays.
        return $this->render('BethelTutorLabsBundle:Observer:observer_statistics_load.html.twig',
            array(
                    'totalVisitsArray'  => $totalVisitsArray,
                    'busiestTimeOfDayArray'  => $busiestTimeOfDayArray,
                    'busiestDayArray'  => $busiestDayArray,
                    'busiestTutorArray'  => $busiestTutorArray,
                    'noShowsArray'  => $noShowsArray,
                    'busiestWeekArray'  => $busiestWeekArray,
                    'byCourseArray'  => $byCourseArray,
                    'getStudentEmailArray'  => $getStudentEmailArray,

                    'totalVisits'  => $totalVisits,
                    'busiestTimeOfDay'  => $busiestTimeOfDay,
                    'busiestDay'  => $busiestDay,
                    'busiestTutor'  => $busiestTutor,
                    'noShows'  => $noShows,
                    'busiestWeek'  => $busiestWeek,
                    'byCourse'  => $byCourse,
                    'getStudentEmail'  => $getStudentEmail,
                    'viewAll'   =>  $viewAll,
                )
            );
    }

    //Returns the total number of visits during this time period.
    public function getTotalVisits($appointments){
        //returns the total #, normal, and then walk-in appointments.
        //array = size 3
        $totalAppts = sizeof($appointments);
        $array = array();

        $walkInAppts = 0;
        foreach($appointments as $appointment){
            if($appointment->getCheckIn() == 5)
                $walkInAppts++;
        }
        $normalAppts = $totalAppts - $walkInAppts;
           
        array_push($array, "Total Visits: ".$totalAppts);
        array_push($array, "Appointments: ".$normalAppts);
        array_push($array, "Walk-ins: ".$walkInAppts);
        return $array;
    }

    public function getBusiestTimeOfDay($appointments){
        $helper = $this->get('wchelper');
        $repository = $helper->getWCScheduleRepository();

        $scheduleArray = $repository->findAll();
        $array = array();

        foreach($appointments as $appointment){ //for each appointment
            foreach($scheduleArray as $timeslot){ //for each timeslot
                //format the times correctly.
                $start = $appointment->getStartTime();
                $start = strtotime($start->format('g:i a'));
                
                $end = $appointment->getEndTime();
                $end = strtotime($end->format('g:i a'));

                $startTimeSlot = $timeslot->getTimeStart()->format('g:i a');
                $startTimeSlotInt = strtotime($startTimeSlot);
                
                $endTimeSlot = $timeslot->getTimeEnd()->format('g:i a');
                $endTimeSlotInt = strtotime($endTimeSlot);

                //create new slot
                $key = $startTimeSlot." - ".$endTimeSlot;
                if(!array_key_exists($key, $array))
                    $array[$key] = 0;

                //if it is within a slot, add 1.
                if($start - $startTimeSlotInt >= 0 AND $end - $endTimeSlotInt <= 0){ 
                    $array[$key] = $array[$key] + 1;
                    break; //each appointment only falls under 1 timeslot, so then break.
                }
            }
        }
        arsort($array);
        return $array;
    }

    //get the schedule in the form 12:35 pm - 1:40 pm
    public function getFormatSchedule($scheduleArray){
        $array = array();
        $count = 0;
        foreach($scheduleArray as $timeslot){
            $start = $timeslot->getTimeStart();
            $start = $start->format('g:i a');
            
            $end = $timeslot->getTimeEnd();
            $end = $end->format('g:i a');

            $array[$count] = $start." - ".$end;
            $count++;
        }

        return $array;
    }

    //Returns an array of the days that students have attended and how many appointments there were.
    public function getBusiestDay($appointments){
        $helper = $this->get('wchelper');

        $array = array();

        foreach($appointments as $appointment){
            $date = $appointment->getStartTime()->format('M j');
            if(!array_key_exists($date, $array))
                $array[$date] = 0;
            $array[$date] = $array[$date] + 1;
        }
        arsort($array);
        return $array;
    }

    //Returns an array of tutors with how many appointments they have worked within a given date range.
    public function getBusiestTutor($appointments){
        $helper = $this->get('wchelper');
        $repository = $helper->getWCScheduleRepository();

        $array = array();

        foreach($appointments as $appointment){ //for each appointment
            $tutorUsername = $appointment->getTutorUsername();
            if(array_key_exists($tutorUsername, $array))
                $array[$tutorUsername] = $array[$tutorUsername] + 1;
            else{
                $array[$tutorUsername] = 1;
            }
        }
        //order by most!
        arsort($array);

        return $array;
    }

    //Since the original filter removes all no shows, we need to pass in $multilingual, $startdate, and $enddate to re-get the no shows.
    public function getNoShows($multilingual, $multilingualRadio, $startdate, $enddate){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        $repository = $helper->getWCAppointmentRepository();
        if($multilingualRadio == 'all'){
            $query = $repository->createQueryBuilder('p')
                ->where("p.CompletedTime >= :start AND p.CompletedTime <= :end AND p.CheckIn LIKE :StudentShowedUp")
                ->setParameters(array('start' => $startdate, 'end' => $enddate, 'StudentShowedUp' => '4')) //4 means no show
                ->getQuery();
        }
        else{
            $query = $repository->createQueryBuilder('p')
                ->where("p.CompletedTime >= :start AND p.CompletedTime <= :end AND p.multilingual = :multilingual AND p.CheckIn LIKE :StudentShowedUp")
                ->setParameters(array('start' => $startdate, 'end' => $enddate, 'multilingual' => $multilingual, 'StudentShowedUp' => '4')) //4 means no show
                ->getQuery();
        }
        $data = $query->getResult();
        return sizeOf($data);
    }

    public function getBusiestWeek($appointments, $start, $end){
        $helper = $this->get('wchelper');
        
        //Create the Dates
        $startdate = date_create($start);
        $enddate = date_create($end);

        $weeks = $this->createWeekArray($start, $end);

        //loop through the weeks and add appointment values as necessary.
        foreach($appointments as $appointment){ //For each appointment
            $date = $appointment->getStartTime();
            //if the appointment is within the specified date range.
            if($startdate < $date AND $date < $enddate){ 
                foreach($weeks as $week=>$value){ //For each Week

                    //////////////////////////////////////////////////////////
                    //This code is to Put the appointment in a week time slot.
                    //Pretty nasty, but it does the trick.
                    $tempArray = split(" - ", $week);
                    $startWeek = $tempArray[0];
                    $endWeek = $tempArray[1];

                    //This case is to check the year jump from Dec to Jan. Jan 22nd will technically be before ANY Dec, no matter the year. This prevents that.
                    if(date_create($startWeek)->format('M') == "Dec" && date_create($endWeek)->format("M") == "Jan"){
                        if(date_create($startWeek) > $date AND $date < date_create($endWeek)){
                            $weeks[$week] = $weeks[$week] + 1; //Add 1 to the slot.
                            break;
                        }
                    }
                    elseif(date_create($startWeek) < $date AND $date < date_create($endWeek)){
                        $weeks[$week] = $weeks[$week] + 1; //Add 1 to the slot.
                        break;
                    }
                    ////////////////////////////////////////////////////////////
                }
                
            }
        }
        return $weeks;
    }

    public function createWeekArray($start, $end){
        $startdate = date_create($start);
        $enddate = date_create($end);
        $currentWeek = date_create($start);

        //Go to the first Saturday.
        $nextWeek = date_create($start);
        $firstSaturday = date_create($start);
        $numToNextSaturday = 6 - (int)$firstSaturday->format("w");
        $nextWeek->add(new DateInterval('P'.$numToNextSaturday.'D'));


        if($nextWeek < $enddate){
            //First Iteration.
            $key = $currentWeek->format('m/j/Y')." - ".$nextWeek->format('m/j/Y');
            $weeks[$key] = 0;
                
            //2nd Iteration. Make the current go to a monday and next go 1 week ahead.
            $numToNextMonday = (7 - (int)$firstSaturday->format("N")); //returns 1 - 7, representing Sun through Sat
            if($numToNextMonday == 7) // if it is monday, don't move any days forward.
                $numToNextMonday = 0;
            $currentWeek->add(new DateInterval('P'.$numToNextMonday.'D'));
            $nextWeek->add(new DateInterval('P1W'));    //adds 1 week
        }

        //Middle Iterations.
        while ($nextWeek < $enddate) {  
            $key = $currentWeek->format('m/j/Y')." - ".$nextWeek->format('m/j/Y');
            $weeks[$key] = 0;
            $currentWeek->add(new DateInterval('P1W')); //adds 1 week
            $nextWeek->add(new DateInterval('P1W'));    //adds 1 week
        }

        //Final Iteration.
        if($currentWeek <= $enddate){
            $key = $currentWeek->format('m/j/Y')." - ".$enddate->format('m/j/Y');
            $weeks[$key] = 0;
        }

        return $weeks;
    }

    function getByCourse($appts, $start, $end){
        $helper = $this->get('wchelper');

        // $repository = $helper->getWCAppointmentRepository();
        // $query = $repository->createQueryBuilder('p')
        //     ->where("p.StartTime >= :start AND p.EndTime <= :end AND (p.CheckIn = :NormalAppt OR p.CheckIn = :WalkIn)")
        //     ->setParameters(array('start' => $start, 'end' => $end, 'NormalAppt' => '3', 'WalkIn' => '5')) //3 means normal, 5 means walk-in
        //     ->getQuery();

        // $appts = $query->getResult();

        // If there are appointments.
        if(sizeof($appts) > 0 ){
            $courses = array();
            foreach($appts as $appt){
                if( $appt->getCourseCode() != "other")
                    $courseCode = $appt->getCourseCode().", Section ".$appt->getCourseSection()." ".$appt->getProfUsername();
                else
                    $courseCode = "other";
                if($courseCode == ", Section 1" || $courseCode == NULL){
                    if(array_key_exists("Unmarked Courses", $courses)){
                        $courses["Unmarked Courses"]++;
                    }
                    else{
                        $courses["Unmarked Courses"] = 1;
                    }
                }
                else{
                    if(array_key_exists($courseCode, $courses)){
                        $courses[$courseCode]++;
                    }
                    else{
                        $courses[$courseCode] = 1;
                    }
                }
            }
            // value, high to low, maintains keys
            arsort($courses);
            return $courses;
        }
        else
            return array(); // empty array

        return array(); // dummy return
    }

    //Returns an array of all student's email within the time period.
    public function getStudentEmail($appointments){
        $helper = $this->get('wchelper');
        $repository = $helper->getUserRepository();
        $array = array();
        $studentEmail;

        foreach($appointments as $appointment){ //for each appointment
            $studentUsername = $appointment->getStudUsername();
            if($studentUsername != "")
            {
                //Get the student email.
                $user = $repository->findByUsername($studentUsername);
                if(sizeof($user) > 0)
                   $studentEmail = $user[0]->getEmail();


                //If the student's email is not in the array, put it in the array.
                $hasStudent = false;
                foreach($array as $student){ //for each student in the array.
                    if($student == $studentEmail){ //if true, do not add the student's email to the list.
                        $hasStudent = true; 
                        break;
                    }
                }
                if($hasStudent == false)
                    array_push($array, $studentEmail);
            }
        }
        return $array;
    }
}