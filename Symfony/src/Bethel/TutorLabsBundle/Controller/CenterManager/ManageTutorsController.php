<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Bethel\TutorLabsBundle\Entity\WCAppointmentData;
use DatePeriod;
use DateTime;
use DateInterval;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ManageTutorsController extends Controller{

	public function manageTutorsCallAction(){
        /*  This call uses ajax to create an appointment */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $repository = $helper->getWCAppointmentRepository();
        $userRepository = $helper->getUserRepository();

        $startdate=$request->get('startdate', '');
        $enddate=$request->get('enddate', '');
        $time=$request->get('time', '');
        $tutors=$request->get('tutors', ''); // Array of tutors
        $days=$request->get('days', ''); // Array of days
        $multilingual = $request->get('multilingual', '');
        $dropIn = $request->get('dropIn', '');
        
        $check = false;
        if($multilingual == 'true')
            $multilingual = true;
        else
            $multilingual = false;
        if($dropIn == 'true')
            $dropIn = true;
        else
            $dropIn = false;

        
        
        //Get the start and end time
        $arrayOfTimes = array();
        foreach( $time as $newTime)
        {
            // Make a Date Period. Not sure how to do the interval, so just get every day...?
            $datePeriodStart = new DateTime($startdate);
            $datePeriodEnd = new DateTime($enddate);
            $period =  new DatePeriod( $datePeriodStart, new DateInterval('P1D'), $datePeriodEnd->add(new DateInterval('P1D')) );
            $logger = $this->get('logger');
            $logger->info($newTime);

            list($start, $end) = explode(' - ', $newTime);
            list($startHour, $part2) = explode(':', $start);
            list($startMinute, $startPeriod) = explode(' ', $part2);

            list($endHour, $part2) = explode(':', $end);
            list($endMinute, $endPeriod) = explode(' ', $part2);

            if ($startPeriod == "pm" || $startPeriod == "PM")
            {
                if( $startHour != "12")
                    $startHour = intval($startHour) + 12;
            }
            if ($endPeriod == "pm" || $endPeriod == "PM")
            {
                if( $endHour != "12")
                    $endHour = intval($endHour) + 12;
            }

            array_push($arrayOfTimes, array($startHour, $startMinute, $endHour, $endMinute, $period) );
            $startHour = "";
            $startMinute = "";
            $endHour = "";
            $endMinute = "";
        }
        $manager = $this->getDoctrine()->getManager();
        // for each tutor, insert a record into AppointmentData for each weekday in $days
        // for $time
        foreach ($tutors as $tutorKey => $tutorID) {

            foreach( $arrayOfTimes as $time )
            {
                foreach ($time[4] as $startTime) 
                {
                    echo print_r($time) . "<br />";
                    $startTime->setTime($time[0], $time[1]);
                    $endTime = clone $startTime;
                    $endTime->setTime($time[2], $time[3]);

                    // Because I didn't know how to filter the DatePeriod above, just check
                    // To see if the current day is in our days array.
                    // This could be optimized probably, but the function isn't used a ton anyway.
                    if (in_array($startTime->format("l"), $days)){
                        //Do a check to see if the Tutor already has an appointment!
                        if( $tutorID != 'Drop-in Hours')
                        {   
                            $user = $userRepository->findOneById($tutorID);
                            $username = $user->getUsername();

                            if(!$this->UserDoesNotHaveAppointment($username, $startTime, $endTime))
                            {
                                $newAppt = new WCAppointmentData();
                                $newAppt->setStudUsername('');
                                $newAppt->setTutorUsername($username);
                                $newAppt->setProgram('CAS');
                                $newAppt->setStartTime($startTime);
                                $newAppt->setEndTime($endTime);
                                $newAppt->setCompletedTime(null);
                                $newAppt->setProfEmail('');
                                $newAppt->setCheckIn('-1');
                                $newAppt->setRequestSub("No");
                                $newAppt->setMultilingual($multilingual); 
                                $newAppt->setDropInAppt($dropIn);
                                $manager->persist($newAppt);
                            }
                        }
                    }
                }
            }
            // Flush the repo now that everything is done.
            $helper->flushRepository();
        }
        return new Response();
    }

    public function manageTutorsViewAction(){
        /*  This is the default view for the manage tutors page.  */
        $helper = $this->get('wchelper');
        $slots = $helper->getAvailableTimeslots();
        $tutors = $helper->getAllTutors();

        // Sort Tutors
        $newTutors = array();
        $newTutors = $tutors;
        usort($newTutors, function($a, $b){
            return strnatcmp ($a->getFirstName(), $b->getFirstName());
        });

        // Sort Dates
         usort($slots, function($a, $b){  //A simple compareTo function for time sorting.
            $t1 = strtotime($a->getTimeStart()->format('g:i a'));
            $t2 = strtotime($b->getTimeStart()->format('g:i a'));
            return ($t1 - $t2);
        });

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_manage_tutors.html.twig',
            array('slots'  =>$slots,
                'tutors'   => $newTutors,
            )
        );
    }

    public function manageTutorsCallUpdateAction(){
        /* For the Update Appointment function which is currently removed. */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $rowID=$request->request->get('rowID', '');
        $username=$request->request->get('username', '');
        $repository = $helper->getWCAppointmentRepository();

        $data = $repository->findOneById($rowID);
        $data->getTutorUsername();
        $data->setTutorUsername($username);
        $helper->flushRepository();

        $response = new JsonResponse();
        $response->setData(array(
          // use the object to get the new value just to be safe. It will be the same though
          'username' => $data->getTutorUsername()
        ));
        return $response;

    }

    public function manageTutorsCallUpdateLookUpAction(){
        /* For the Update Appointment function which is currently removed. */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $username=$request->request->get('username', '');
        $user = $helper->getUsersWithUsernameLike($username);
        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.TutorUsername LIKE :username AND p.StudUsername != ''")
          // !! %username% will return anything that containers $username
          // !! So %843% will return both ejc84332, ejc84333 and abc84332.
          ->setParameter('username', '%' . $username . '%')
          ->getQuery();

        $data =  $query->getResult();
        $tutors = $helper->getAllTutors();

        // build the dropdown here isntead of the template so we only have to do it once.
        $select = "<select><option value=''>-select-</option>";
            foreach ($tutors as $key => $value) {
                $select .= "<option value='$value'>$value</option>";
            }
        $select .= "</select>";

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_manage_tutors_update_lookup.html.twig',
            array(
                'data'        => $data,
                'select'      => $select
            )
        );
    }

    public function manageTutorsCallRemoveAction(){
        /* Function to remove a selected Tutor Schedule. */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $appointmentID=$request->request->get('appointmentID', '');
        $repository = $helper->getWCAppointmentRepository();

        $data = $repository->findOneById($appointmentID);

        if (!$data){
            return new Response();
        }

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->remove($data);
        $entityManager->flush();

        return new Response("Success");

    }
    
    public function manageTutorsCallLookUpAction(){
        /* For the Lookup Tutor Schedule function */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request

        $tutors = $request->request->get('tutors', '');

        $today = date('Y-m-d H:i:s a', time());

        if(empty($tutors))
            return new Response();
        else{
            $appointments = array();
            foreach($tutors as $tutor){
                $repository = $helper->getWCAppointmentRepository();
                $query = $repository->createQueryBuilder('p')
                  ->where('p.TutorUsername = :username AND p.StartTime > :today')
                  ->setParameters(array('username' => $tutor, 'today' => $today))
                  ->getQuery();

                $normalappts =  $query->getResult();
                $appointments = array_merge($appointments, $normalappts);
            }
        }

        $json = $helper->getAppointmentsAsJSON($appointments);
        return $helper->makeJSONResponse($json);
    }

    public function UserDoesNotHaveAppointment($tutorUsername, $startTime, $endTime){
        $helper = $this->get('wchelper');

        
        //get all appointments this tutor has.
        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
            ->where('p.TutorUsername LIKE :tutorusername AND p.StartTime = :start AND p.EndTime = :end')
            ->setParameters(array('tutorusername' => '%' . $tutorUsername . '%', 'start' => $startTime, 'end' => $endTime))
            ->getQuery();
        $appointment = $query->getResult();
        
        if(sizeof($appointment) == 0)
            return false;
        return true;
    }
}