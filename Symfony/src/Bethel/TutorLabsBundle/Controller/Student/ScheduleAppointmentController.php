<?php

namespace Bethel\TutorLabsBundle\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use DatePeriod;
use DateTime;
use DateInterval;

class ScheduleAppointmentController extends Controller{

    public function scheduleViewAction(){
        /* Standard view function. */
      return $this->render('BethelTutorLabsBundle:Student:student_schedule_appointment.html.twig');
    }

    public function scheduleLoadAction(){
        /* This takes in the date and displays all available appointments */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $user = $helper->getCurrentUser();

    //Appointments too close to the 'Threshhold time' do not get loaded.
        $repository = $helper->getWCSystemSettingsRepository();
        $query = $repository->createQueryBuilder('p')
          ->select('p')
          ->setMaxResults( 1 )
          ->getQuery();
         $data = $query->getResult();

        $timeLimit = $data[0]->getTimeLimit();
        
    //Threshhold time needs to be X hours before the appointment time, where X is from system settings.
        $secondsInHour = 60*60;
        $threshholdtime = date_create(date('Y-m-d H:i:s', time()+($timeLimit*$secondsInHour)));

        $appointments = array();
        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.StudUsername = :StudUsername AND p.StartTime > :threshholdtime")
          ->setParameters(array('StudUsername' => '', 'threshholdtime' => $threshholdtime))
          ->getQuery();

        $appointments = $query->getResult();

        $json = $helper->getAppointmentsAsJSON($appointments);
        return $helper->makeJSONResponse($json);
    }



    public function scheduleCallAction(){
        /* A student signs up for a specific appointment. */
        $helper = $this->get('wchelper');
        $wsapi = $this->get('wsapi');
        $request = $helper->getRequest();
        $appointmentid = intval($request->request->get('ID', ''));
        $assignment = $request->request->get('assignment', '');
        $username=$helper->getCurrentUser()->getUsername();
    /*///////////////////////////////////////////////
    Before anything, check to see if the student is CAPS/GS
    ///////////////////////////////////////////////*/
        //Checks to see if the user can be found with banner.
        //If the user is not, return an error message.
        $roles = $wsapi->getRoles($username);

        // Gather roles to make sure student is not CAPS/GS
        $rolesArray = array();
        foreach( $roles as $role){
            array_push($rolesArray, $role["userRole"]);
        }

        // If student is CAPS/GS, 
        if( in_array("STUDENT-CAPS", $rolesArray) && !in_array("STUDENT-CAS", $rolesArray))
            return new JsonResponse("This site manages appointments for the CAS Writing Center. Graduate and CAPS students should contact the CAPS/GS Academic Resource Center to schedule appointments with writing consultants. Our apologies for any inconvenience.");
        elseif( in_array("STUDENT-GS", $rolesArray) && !in_array("STUDENT-CAS", $rolesArray))
            return new JsonResponse("This site manages appointments for the CAS Writing Center. Graduate and CAPS students should contact the CAPS/GS Academic Resource Center to schedule appointments with writing consultants. Our apologies for any inconvenience.");
        elseif( !in_array("STUDENT-CAS", $rolesArray) )
            return new JsonResponse("This site manages appointments for the CAS Writing Center. Only CAS students are allowed to access the Writing Center. Our apologies for any inconvenience.");
        elseif( sizeof($roles) == 0){
            return new JsonResponse("Failed");
        }

    /*///////////////////////////////////////////////
    First, check to see if the student is banned.
    ///////////////////////////////////////////////*/
        $banRepository = $helper->getWCStudentBansRepository();
        $query = $banRepository->createQueryBuilder('p')
          ->where("p.username LIKE :username")
          ->setParameter('username', '%'.$username.'%')
          ->getQuery();

        $data = $query->getResult();
          if (sizeof($data) != 0) {
               $data = $data[0];

              //Prevent the student from signing up, and say that he/she is banned.
               return new Response("You are currently 'banned' from signing up for new appointments. Please contact the Writing Center if you would like to sign up for an appointment.");
           }
    /*//////////////////////////////////////////////////////
    Second, check to see if they are allowed to sign up.
    //////////////////////////////////////////////////////*/
      //Get Appointment Limit for one week.
        $settingsRepository = $helper->getWCSystemSettingsRepository();
        $query = $settingsRepository->createQueryBuilder('p')
          ->select('p')
          ->setMaxResults( 1 )
          ->getQuery();
         $data = $query->getResult();

        $apptLimit = $data[0]->getApptLimit();

        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.id LIKE :id")
          ->setParameter('id', $appointmentid)
          ->setMaxResults( 1 )
          ->getQuery();
         $appointment = $query->getResult();
         if( sizeof($appointment) > 0)
         {
            $appointment = $appointment[0];
            $startDate = $appointment->getStartTime();
            $dayOfWeek = $startDate->format('l');
            $dayOfWeek = $helper->dayToNumber($dayOfWeek);  //returns 1-7 Sunday/Saturday.

            // This is checking to see how many appts a student has signed up for this week.
            //Get the day of the week. From that figure out how many days are before/after it.
            //Then time() - daysbefore and time() + daysafter will be the query limits.

            $secondsInDay = 60*60*24;
            $StartOfWeek = date_create(date('Y-m-d H:i:s', strtotime($startDate->format('Y-m-d'))-($dayOfWeek-1)*$secondsInDay));
            $EndOfWeek = date_create(date('Y-m-d H:i:s', strtotime($startDate->format('Y-m-d'))+(1+7-$dayOfWeek)*$secondsInDay));

            $repository = $helper->getWCAppointmentRepository();
            $query = $repository->createQueryBuilder('p')
              ->where("p.StudUsername LIKE :username AND p.StartTime > :StartOfWeek AND p.StartTime < :EndOfWeek")
              ->setParameters(array('username' => '%'.$username.'%', 'StartOfWeek' => $StartOfWeek, 'EndOfWeek' => $EndOfWeek))
              ->getQuery();
             $data = $query->getResult();

             $numberOfAppts = sizeof($data); //Number of appts a student has signed up for this week.
             if($numberOfAppts >= $apptLimit)
             {
              return new Response("You have signed up for this weeks limit for appointments. Contact ".$helper->getCMName()." if you would like to sign up for more appointments.");
             }
         }

         // students cannot sign up for concurrent appts. If they have, return a response.
         $concurrentAppts = 0;

         $repository = $helper->getWCAppointmentRepository();

         $query = $repository->createQueryBuilder('p')
          ->where("p.id = :id")
          ->setParameter('id', $appointmentid)
          ->getQuery();
          $appt = $query->getResult();

          if( sizeof($appt) > 0)
          {
              $query = $repository->createQueryBuilder('p')
              ->where("p.StartTime = :startDate")
              ->setParameter('startDate', $appt[0]->getStartTime())
              ->getQuery();
              $appts = $query->getResult();
              
              foreach( $appts as $appt)
              {
                if( $appt->getStudUsername() == $username)
                  $concurrentAppts++;
              }

             if($concurrentAppts >= 1)
             {
                return new Response("We are sorry, but you cannot sign up for concurrent appointments.");
             }
         }

    /*//////////////////////////////////////////////////////
    Third, sign up the student.
    //////////////////////////////////////////////////////*/
        
        $repository = $helper->getWCAppointmentRepository();

        $query = $repository->createQueryBuilder('p')
          ->where("p.id = :id and p.StudUsername = ''")
          ->setParameter('id', $appointmentid)
          ->getQuery();

        $appt = $query->getResult();

        // Check to make sure that no appt can have 2 students sign up at the same time!
        
          if (sizeof($appt) == 0) {
               return new Response("We are sorry, but there was an error with the request. Please refresh and try again.");
           }
        $appt = $appt[0];
        $appt->setStudUsername($username);
        $appt->setAssignment($assignment);

        $helper->flushRepository();

        //Now email the tutor, telling him or her that a student has signed up.
        $tutorUsername = $appt->getTutorUsername();
        $email_pref = $helper->findEmailPrefByUsername($tutorUsername);
        if( $email_pref != "Email preferences for this user are not set up.")
        {
          if($email_pref->getStudentSignUpEmail())
          {
            $this->SendEmail($appt);
          }
        }

        //get student email.
        $repository = $helper->getUserRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.username LIKE :username")
          ->setParameter('username', '%'.$username.'%')
          ->getQuery();
        $student = $query->getResult();
        $student = $student[0];

        ///////////////////// Email the student here. //////////////////////////
    
        $this->emailStudent($appt, $student->getEmail());
        ////////////////////////////////////////////////////////////////////////

        $helper->flushRepository();
        return new Response("Success");
    }

    /* Displays an alert if all appointments are full. */
    public function scheduleCheckEmptyAction(){
        $helper = $this->get('wchelper');

        return new Response($helper->checkIfScheduleEmpty());
    }

    public function SendEmail($appt){
        $helper = $this->get('wchelper');

        $request = $helper->getRequest();
        $tutorUsername = $appt->getTutorUsername();
        $repository = $helper->getUserRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.username LIKE :tutorUsername")
          ->setParameter('tutorUsername', '%'.$tutorUsername.'%')
          ->getQuery();

        $tutor = $query->getResult();
        $email = $tutor[0]->getEmail();
        $subject = "Writing Center: Student Sign Up";

        //body
        $Student = "Student: ".$helper->getFirstLastNameByUsername( $appt->getStudUsername() )."\n";
        $StartTime = "Start Time: ".$appt->getStartTime()->format("m/d/Y g:i a")."\n";
        $EndTime = "End Time: ".$appt->getEndTime()->format("m/d/Y g:i a")."\n";
        $assignment = "Assignment: ".$appt->getAssignment()."\n";
        $Multilingual = "";
        if($appt->getMultilingual())
          $Multilingual = "This is a Multilingual Appointment.\n";
        $body = "Hello! A student has signed up for one of your appointments at the Writing Center.\n\n".$Student.$StartTime.$EndTime.$assignment.$Multilingual;
        $helper->sendMessage($email, $subject, $body);

        return;
    }
    public function emailStudent($newAppt, $email){
        $helper = $this->get('wchelper');

        $studentusername = $newAppt->getStudUsername();
        $tutorusername = $newAppt->getTutorUsername();

        $studentemail = $email;

        $subject = "Writing Center: Appointment Reminder";

        $body = "Thank you for signing up for an appointment with the Writing Center. Here are the details of the appointment:\n\n";
        $body .= "Tutor: " . $helper->getFirstLastNameByUsername($tutorusername) . "\nStart Time: ".$newAppt->getStartTime()->format("m/d/Y g:i a")."\nEnd Time: ".$newAppt->getEndTime()->format("m/d/Y g:i a");
        if( $newAppt->getMultilingual() )
          $body .= "\nMultilingual: ".$newAppt->getMultilingual();
    
        $helper->sendMessage($studentemail, $subject, $body);
        return;
    }
}
