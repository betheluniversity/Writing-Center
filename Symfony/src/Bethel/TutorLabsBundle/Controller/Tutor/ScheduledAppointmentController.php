<?php

namespace Bethel\TutorLabsBundle\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Bethel\TutorLabsBundle\Entity\WCStudentBans;
use Bethel\UserBundle\Entity\User;
use Bethel\TutorLabsBundle\Entity\WCAppointmentData;
use Bethel\TutorLabsBundle\Entity\WCEmailPreferences;
use Bethel\TutorLabsBundle\Form\WCWalkInStudentType;
use DatePeriod;
use DateTime;
use DateInterval;

class ScheduledAppointmentController extends Controller{

    //Check In Values
            // -1 - Appointment has been made and nothing has happened since
            // 0 - Tutor Signs in and Student has not showed up
            // 1 - Tutor and Student are signed in
            // 2 - Student has Signed out.
        //Final values    //Final values
            // 3 - Normal appointment.
            // 4 - No Show
            // 5 - Walk In
            //Tutor signs in.

    // base page
    public function scheduledAppointmentViewAction(){
        return $this->render('BethelTutorLabsBundle:Tutor:tutor_scheduled_appointment.html.twig');
    }

    //Creates the array of Appointments the Tutor is a part of.
    public function scheduledAppointmentCallAction(){
       $helper = $this->get('wchelper');
       $request = $helper->getRequest();
       $repository = $helper->getWCAppointmentRepository();
       // get username from POST request
       $TutorUsername=$request->request->get('TutorUsername', '');

       $currentTime = date_create(date('Y-m-d', time()));

        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername != :studUsername AND p.TutorUsername LIKE :tutorusername AND p.CheckIn < :checkIn AND p.StartTime >= :currentTime')
        ->setParameters(array('studUsername' => "", 'tutorusername' => '%' . $TutorUsername . '%','checkIn' => 3, 'currentTime' => $currentTime))
        ->orderBy('p.StartTime', 'ASC')
        ->getQuery();
        
       $users = $query->getResult();

       $names = array();
        $repository = $helper->getUserRepository();
        foreach( $users as $user){
            $query = $repository->createQueryBuilder('p')
                ->where('p.username = :user')
                ->setParameters(array('user' => $user->getStudUsername() ))
                ->getQuery();

            $newUser = $query->getSingleResult();
            array_push($names, $newUser->getFirstName() . ' ' . $newUser->getLastName());
        }

        return $this->render('BethelTutorLabsBundle:Tutor:tutor_scheduled_appointment_call.html.twig',
            array(
                'users'              =>  $users,
                'names'              => $names
            )
        );
    }

    //Starts an appointment. (when you click on 'Start' or 'Continue')
    public function scheduledAppointmentCallTutorInAction(Request $request){
       $helper = $this->get('wchelper');
       $request = $helper->getRequest();

       $currentTime = date_create(date('Y-m-d H:i:s', time()));

       // get username from POST request
       $TutorUsername=$request->request->get('TutorUsername', '');
       $id=$request->request->get('id', '');
        $data = new WCAppointmentData();

        $form = $this->createForm(new WCWalkInStudentType(), $data);
        $form->handleRequest($request);

        if($form->isValid())
        {
           //Get Appointment That We are Working With
            $data = $this->getAppointmentByID($form->get('id')->getData());

            if (sizeof($data) == 0) 
            {
                return new Response("no results were found for appointmentID=".$id);
            }
            $data = $data[0];
            
            $data->setStudentSignOut($currentTime);
            $data->setCheckIn(3);
            $data->setCompletedTime($currentTime);
            $data->setRequestSub("No");
            $data->setCourseCode($form->get('CourseCode')->getData());
            $data->setCourseSection($form->get('CourseSection')->getData());
            $data->setAssignment($form->get('Assignment')->getData());
            $data->setComment($form->get('Comment')->getData());
            $data->setSuggestion($form->get('Suggestion')->getData());
            $data->setMultilingual($form->get('Multilingual')->getData());
            
            //////////////////// Email //////////////////////////////////////
            $ferpaEmail = $form->get('ferpaAgreement')->getData();
            $CourseCode = $data->getCourseCode();
            $profEmail = $helper->getProfEmail($data->getStudUsername(), $CourseCode);
            $data->setProfEmail($profEmail);
            $profUsername = $helper->getProfUsername($form->get('StudUsername')->getData(), $CourseCode);

            $data->setProfUsername($profUsername);

            if( $CourseCode != 'other')
                $profEmail = $helper->getProfEmail($form->get('StudUsername')->getData(), $CourseCode);
            else
                $profEmail = "";
            $helper->emailTuteeAndInstructor($profEmail, $form->get('email')->getData(), $data, $ferpaEmail);
            /////////////////////////////////////////////////////////////////
            $helper->flushRepository();

            ////////////////////////////
            // Qualtrics
            ///////////////////////////
            $repository = $helper->getWCSystemSettingsRepository();
            $query = $repository->createQueryBuilder('p')
              ->select('p')
              ->setMaxResults( 1 )
              ->getQuery();
             $data = $query->getResult();

            $qualtricsLink = $data[0]->getQualtricsLink();

            //return some kind of success message here
            return $this->render('BethelTutorLabsBundle:Tutor:tutor_appointment_exit_page.html.twig', 
                array(
                    'qualtricsLink' => $qualtricsLink,
                )
            );
        }
        else{
            //Get Appointment That We are Working With
            $data = $this->getAppointmentByID($id);

            if (sizeof($data) == 0) 
            {
                return new Response("no results were found for appointmentID=".$id);
            }
            $data = $data[0];

            $checkIn = $data->getCheckIn();

            //get student first/last names.
            $studentusername = $data->getStudUsername();
            $student = $helper->getUsersWithUsernameLike($studentusername);
            //Create a new user here.

            $student = $student[0];
            $firstName = $student->getFirstName();
            $lastName = $student->getLastName();
            $StudEmail = $student->getEmail();
            // Get courses in a html format.6
            $courses = $this->getCourseCodes($studentusername);

            if($data->getStudentSignIn() == NULL)
                $data->setStudentSignIn($currentTime);
            $data->setCheckIn(1);

            $data->setActualStartTime($currentTime);
            $helper->flushRepository();
            
            $form = $this->createForm(new WCWalkInStudentType(), $data);
        }

        return $this->render('BethelTutorLabsBundle:Tutor:tutor_scheduled_appointment_form.html.twig',
            array(
                'ID'            => $data->getID(),
                'form'          => $form->createView(),
                'StudUsername'  => $studentusername,
                'firstName'     =>   $firstName,
                'lastName'      =>   $lastName,
                'StudEmail'     =>   $StudEmail,
                'currentTime'   =>  $currentTime,
                'courses'       => $courses,
            )
        );
    }

    public function getAppointmentByID($id){
        $helper = $this->get('wchelper');
        $repository = $helper->getWCAppointmentRepository();
        $queryAppointment = $repository->createQueryBuilder('p')
                ->where("p.id = :id")
                ->setParameter('id', $id)
                ->getQuery();
        $data =  $queryAppointment->getResult();
        return $data;
    }

    public function getCourseCodes($studentUsername){
        $wsapi = $this->get('wsapi');
        $htmlCourses = "<label for='course-select'>Click the course that the student is here for.</label><select name='course-select' id='course-select' size='8' style='width:60%'>";
        $courses = $wsapi->getCourses($studentUsername);
        $checkDuplicates = array();
        foreach ($courses as $course => $info) {
            $courseCode = $info['subject'].$info['cNumber'];
            $courseSection = $info['section'];
            $courseTitle = $info['title'];
            $courseProf = $info['instructor'];
            $newValue = $courseTitle.", ".$courseCode." section ".$courseSection.", ".$courseProf;
            if(!in_array($newValue, $checkDuplicates)){ //Does not allow for duplicates to be added. (BANNER sometimes has duplicates)
                array_push($checkDuplicates, $newValue);
                $htmlCourses .= "<option value='$courseCode $courseSection' selected='selected'>$newValue</option>";
            }
        }
        $htmlCourses .= "<option value='other' selected='selected'>Other (scholarship essays, graduate school applications, etc)</option>";
        $htmlCourses .= "</select>";
        return $htmlCourses;
    }

    //When you push the no show button.
    public function scheduledAppointmentNoShowAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $TutorUsername=$request->request->get('TutorUsername', '');
        $id=$request->request->get('id', '');

        $repository = $helper->getWCAppointmentRepository();
        $queryAppointment = $repository->createQueryBuilder('p')
                ->where("p.id = :id")
                ->setParameter('id', $id)
                ->getQuery();
        $appt = $queryAppointment->getResult();
    
        if(sizeof($appt) == 0)
            return new Response("The Student was unable to be marked as a no-show.");

        $appt = $appt[0];

        $appt->setCheckIn("4");
        $appt->setCompletedTime( date_create(date('Y-m-d H:i:s', time())));
        $helper->flushRepository();
        $StudUsername = $appt->getStudUsername();
        ////////////////////////// Ban the student /////////////////////

        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
            ->where('p.StudUsername LIKE :studentusername', 'p.CheckIn LIKE :CheckIn')
            ->setParameters(array('studentusername' => '%' . $StudUsername . '%', 'CheckIn' => 4)) //4 == noshow.
            ->getQuery();
         $user =  $query->getResult(); 

        $repository = $helper->getWCStudentBansRepository();
        $query = $repository->createQueryBuilder('p')
            ->where('p.username LIKE :studentusername')
            ->setParameters(array('studentusername' => '%' . $StudUsername . '%'))
            ->getQuery();
         $banned = $query->getResult(); 

        $repository = $helper->getWCSystemSettingsRepository();
        $query = $repository->createQueryBuilder('p')
          ->select('p')
          ->setMaxResults( 1 )
          ->getQuery();
         $data = $query->getResult();

        $banLimit = $data[0]->getBanLimit();
         //The student is already banned, so you don't need to ban him/her again
        if(sizeof($user) >= $banLimit)
         {
            $manager = $this->getDoctrine()->getManager();
            $repository = $helper->getWCStudentBansRepository();
            $banStudent = new WCStudentBans();
            $banStudent->setUsername($StudUsername);
            $banStudent->setbannedDate(date_create(date('Y-m-d H:i:s', time())));

            $manager->persist($banStudent);
            $manager->flush();
         }


        $helper->flushRepository();
        return $this->render('BethelTutorLabsBundle:Tutor:tutor_scheduled_appointment_noshow_page.html.twig');
    }

    public function emailStudent($newAppt, $email){
        $helper = $this->get('wchelper');

        // Temporarily change the array so that each has a first/last name.

        $studentusername = $newAppt->getStudUsername();
        $tutorusername = $newAppt->getTutorUsername();

        $studentemail = $email;

        $subject = "Writing Center: Appointment";

        $body = "Thank you for attending the Writing Center. Here are the details of the appointment:\n\n";
        $body = $body."Tutor: ".$helper->getFirstLastNameByUsername( $tutorusername )."\nTime: ".$newAppt->getStartTime()->format("m/d/Y g:i a");
        $body = $body."\nMultilingual: ".$newAppt->getMultilingual();
        $body = $body."\n\nAssignment: ".$newAppt->getAssignment()."\nComments: ".$newAppt->getComment()."\nSuggestions: ".$newAppt->getSuggestion();
    
        $helper->SendMessage($studentemail, $subject, $body);
        return;
    }
 }