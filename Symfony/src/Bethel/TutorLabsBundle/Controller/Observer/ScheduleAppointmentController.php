<?php

namespace Bethel\TutorLabsBundle\Controller\Observer;

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
      return $this->render('BethelTutorLabsBundle:Observer:observer_schedule_appointment.html.twig');
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
        $repository = $helper->getWCAppointmentRepository();
        
        
        if($user->getMultilingual())
        {
            $query = $repository->createQueryBuilder('p')
              ->where("p.StudUsername = :StudUsername AND p.StartTime > :threshholdtime")
              ->setParameters(array('StudUsername' => '', 'threshholdtime' => $threshholdtime))
              ->getQuery();
   
            $data = $query->getResult();
        }
        else{
          $query = $repository->createQueryBuilder('p')
            ->where("p.StudUsername = :StudUsername AND p.StartTime > :threshholdtime AND p.multilingual = :multi")
            ->setParameters(array('StudUsername' => '', 'threshholdtime' => $threshholdtime, 'multi' => false))
            ->getQuery();
   
          $data =  $query->getResult();
        }

        $json = $helper->getAppointmentsAsJSON($data);
        return $helper->makeJSONResponse($json);
    }



    public function scheduleCallAction(){
        /* A student signs up for a specific appointment. */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $username=$helper->getCurrentUser()->getUsername();
    /*///////////////////////////////////////////////
    First, check to see if the student is banned.
    ///////////////////////////////////////////////*/
        $repository = $helper->getWCStudentBansRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.username LIKE :username")
          ->setParameter('username', '%'.$username.'%')
          ->getQuery();

        $data = $query->getResult();
          if (sizeof($data) != 0) {
               $data = $data[0];

              //Prevent the student from signing up, and say that he/she is banned.
               return new Response("You are currently 'banned' from signing up for new appointments. Contact ".$helper->getCMName()." if you would like to sign up for an appointment.");
           }
    /*//////////////////////////////////////////////////////
    Second, check to see if they are allowed to sign up.
    //////////////////////////////////////////////////////*/
      //Get Appointment Limit for one week.
        $repository = $helper->getWCSystemSettingsRepository();
        $query = $repository->createQueryBuilder('p')
          ->select('p')
          ->setMaxResults( 1 )
          ->getQuery();
         $data = $query->getResult();

        $apptLimit = $data[0]->getApptLimit();

      
      //Get the day of the week. From that figure out how many days are before/after it.
      //Then time() - daysbefore and time() + daysafter will be the query limits.
        $startTime=$request->request->get('startTime', '');
        $startDate = new DateTime($startTime);
        $dayOfWeek = $startDate->format('l');
        $dayOfWeek = $helper->dayToNumber($dayOfWeek);  //returns 1-7 Sunday/Saturday.

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

    /*//////////////////////////////////////////////////////
    Third, sign up the student.
    //////////////////////////////////////////////////////*/
        $id = intval($request->request->get('ID', ''));
        $repository = $helper->getWCAppointmentRepository();

        $query = $repository->createQueryBuilder('p')
          ->where("p.id = :id")
          ->setParameter('id', $id)
          ->getQuery();

        $appt = $query->getResult();
          if (sizeof($appt) == 0) {
               return new Response("no results were found for ID=".$id);
           }
        $appt = $appt[0];
        $appt->setStudUsername($username);

        $helper->flushRepository();

        //Now email the tutor, telling him or her that a student has signed up.
        $tutorUsername = $appt->getTutorUsername();
        $email_pref = $helper->findEmailPrefByUsername($tutorUsername);
        if($email_pref->getStudentSignUpEmail())
        {
          $this->SendEmail($appt);
        }

        //Now email the student, reminding him or her that he or she signed up.
        $studentusername = $username;
        $emailpref = $helper->findEmailPrefByUsername($studentusername);
        //get student email.
        $repository = $helper->getUserRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.username LIKE :username")
          ->setParameter('username', '%'.$username.'%')
          ->getQuery();

        $user = $query->getResult();
        $email = $user[0]->getEmail();
        $this->emailStudent($appt, $email);

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
        $StudentUsername = "Student Username: ".$appt->getStudUsername()."\n";
        $StartTime = "Start Time: ".$appt->getStartTime()->format("g:i a m-d-Y")."\n";
        $EndTime = "End Time: ".$appt->getEndTime()->format("g:i a m-d-Y")."\n";
        $Multilingual = "";
        if($appt->getMultilingual())
          $Multilingual = "This is a Multilingual Appointment.\n";
        $body = "Hello! A student has signed up for one of your appointments at the Writing Center.\n\n".$StudentUsername.$StartTime.$EndTime.$Multilingual;
        $helper->sendMessage($email, $subject, $body);

        ///////////////////// Email the student here. //////////////////////////
        $studentusername = $appt->getStudUsername();
        $emailpref = $helper->findEmailPrefByUsername($studentusername);
    
        if($emailpref->getReceiveFeedbackEmail()){
            $this->emailStudent($appt, $email);
        }
        ////////////////////////////////////////////////////////////////////////

        return;
    }
    public function emailStudent($newAppt, $email){
        $helper = $this->get('wchelper');
        $studentusername = $newAppt->getStudUsername();
        $tutorusername = $newAppt->getTutorUsername();

        $studentemail = $email;
        $studentemail = 'ces55739@bethel.edu';  //   ************* Remove this when it goes live ********* //

        $subject = "Writing Center: Appointment Reminder";

        $body = "Thank you for signing up for an appointment with the Writing Center. Here are the details of the appointment:\n\n";
        $body = $body."Tutor Username: ".$tutorusername."\nTime: ".$newAppt->getStartTime()->format("m/d/Y g:i a");
        $body = $body."\nMultilingual: ".$newAppt->getMultilingual();
        $body = $body."\n\nAssignment: ".$newAppt->getAssignment()."\nComments: ".$newAppt->getComment()."\nSuggestions: ".$newAppt->getSuggestion();
    
        $helper->SendMessage($studentemail, $subject, $body);
        return;
    }
}