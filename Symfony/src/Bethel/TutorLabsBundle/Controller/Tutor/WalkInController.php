<?php

namespace Bethel\TutorLabsBundle\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Bethel\TutorLabsBundle\Entity\WCAppointmentData;
use Bethel\UserBundle\Entity\User;
use Bethel\TutorLabsBundle\Entity\WCEmailPreferences;
use Bethel\TutorLabsBundle\Form\WCWalkInStudentType;

class WalkInController extends Controller{

    // base page
    public function walkInViewAction(){
        //This should actually create a form to allow the student username to be typed in.
        return $this->render('BethelTutorLabsBundle:Tutor:tutor_walk_in.html.twig');
    }

    public function walkInBeginSessionAction(Request $request){
        $helper = $this->get('wchelper');
        $wsapi = $this->get('wsapi');
        $request = $helper->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $studentUsername = $request->request->get('studentUsername', '');
        $tutorUsername = $helper->getCurrentUser();
        
        $newUser = $helper->getUsersWithUsernameLike($studentUsername);

        ////////////////////Create a new user here.//////////////////////
        if(sizeof($newUser) == 0){ //If user is not found in the current DB
            if($this->createNewUser($studentUsername) == "Success")
                $newUser = $helper->getUsersWithUsernameLike($studentUsername);
            else
                return new JsonResponse("That user is not currently in the system. If you believe this is a mistake, please inform April Schmidt.");
        }
        /////////////////////////////////////////////////////////////////

        $newUser = $newUser[0];
        $firstName = $newUser->getFirstName();
        $lastName = $newUser->getLastName();
        $StudEmail = $newUser->getEmail();

        //Checks to see if the user can be found with banner.
        //If the user is not, return an error message.
        $roles = $wsapi->getRoles($studentUsername);
        
        // Gather roles to make sure student is not CAPS/GS
        $rolesArray = array();
        foreach( $roles as $role){
            array_push($rolesArray, $role["userRole"]);
        }
        // If student is CAPS/GS, 
        if( in_array("STUDENT-CAPS", $rolesArray) && !in_array("STUDENT-CAS", $rolesArray))
            return new JsonResponse("CAPS");
        elseif( in_array("STUDENT-GS", $rolesArray) && !in_array("STUDENT-CAS", $rolesArray))
            return new JsonResponse("GS");
        elseif( !in_array("STUDENT-CAS", $rolesArray) )
            return new JsonResponse("other");
        elseif( sizeof($roles) == 0){
            return new JsonResponse("Failed");
        }

        $newAppt = new WCAppointmentData();
        // Get courses in a html format.
        $courses = $this->getCourseCodes($studentUsername);
        //

        $form = $this->createForm(new WCWalkInStudentType(), $newAppt);
        $form->handleRequest($request);
        return $this->render('BethelTutorLabsBundle:Tutor:tutor_walk_in_begin.html.twig', 
            array( 
                    'form'          => $form->createView(),
                    'StudUsername'  => $studentUsername,
                    'firstName'     =>   $firstName,
                    'lastName'      =>   $lastName,
                    'StudEmail'     =>   $StudEmail,
                    'courses'       => $courses,
                )
        );
    }

    public function getCourseCodes($studentUsername){
        $wsapi = $this->get('wsapi');
        $htmlCourses = "<label for='course-select'>Click the course that the student is here for.</label><select name='course-select' id='course-select' size='8' style='width:60%'>";
        $courses = $wsapi->getCourses($studentUsername);
        $checkDuplicates = array();
        if( $courses){
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
        }
        $htmlCourses .= "<option value='other' selected='selected'>Other (scholarship essays, graduate school applications, etc)</option>";
        $htmlCourses .= "</select>";
        return $htmlCourses;
    }

    public function walkInExitPageAction(Request $request){
        /* Allows Tutors to comment on the appointment. */

        $helper = $this->get('wchelper');
        $tutorUsername = $helper->getUser();

        $newAppt = $this->createNewAppointment($tutorUsername);

        //Create the form to be viewed.
        $form = $this->createForm(new WCWalkInStudentType(), $newAppt);
        $form->handleRequest($request);

        $form->getData();
        if($form->isValid())
        {
            $currentTime = date_create(date('Y-m-d H:i:s', time()));
            $newAppt->setEndTime($currentTime);
            $newAppt->setStudentSignOut($currentTime);
            $newAppt->setCompletedTime($currentTime);
            $newAppt->setCheckIn(5);

            $first = $form->get('first')->getData();
            $last = $form->get('last')->getData();
            $tuteeEmail = $form->get('email')->getData();
            // Set the email!

            $ferpaEmail = $form->get('ferpaAgreement')->getData();
            $CourseCode = $newAppt->getCourseCode();
            $CourseSection = $newAppt->getCourseSection();

            if( $CourseCode != 'other')
                $profEmail = $helper->getProfEmail($form->get('StudUsername')->getData(), $CourseCode);
            else
                $profEmail = "";
            $helper->emailTuteeAndInstructor($profEmail, $tuteeEmail, $newAppt, $ferpaEmail); //Need to include TuteeEmail and ProfEmail.

            $this->createUser($newAppt, $first, $last, $tuteeEmail); //Creates the new user, otherwise do nothing.
            $newAppt->setProfEmail($profEmail);
            $profUsername = $helper->getProfUsername($form->get('StudUsername')->getData(), $CourseCode);
            if( $profUsername == "Failed")
                $profUsername = "";
            $newAppt->setProfUsername($profUsername);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($newAppt);
            $em->flush();

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
        //If the form is submitted and not vaild.
        else
        {
            //return a error message saying it didn't work.
            return new Response("There was an error with the request, please try again. If that doesn't work, contact ".$helper->getCMName().".");
        }
    }

    public function createUser($newAppt,  $first, $last, $email){
        /* Returns a new user or gets the user is one by that username is already created. */
        $manager = $this->getDoctrine()->getManager();
        $helper = $this->get('wchelper');

        $studentUsername = $newAppt->getStudUsername();

        //Query for existing user
        $repository = $helper->getUserRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.username LIKE :username")
          ->setParameter('username', '%'.$studentUsername.'%')
          ->getQuery();

        $data =  $query->getResult();
        if( sizeof($data) == 0) {
            $bannerArray = array( $first, $last, $studentUsername, $email, "password", "ROLE_STUDENT");

            //Create a new Email preferences
            $emailPref = new WCEmailPreferences();

            $user = new User();
            $user->setLab('1');
            $user->setFirstName($bannerArray[0]);
            $user->setLastName($bannerArray[1]);
            //$user->setUsername($bannerArray[2]);
            $user->setUsername($studentUsername);
            $user->setEmail($bannerArray[3]);
            $user->setPlainPassword($bannerArray[4]);
            $user->setRoles( array(
                                        "role" => $bannerArray[5],
                                    ));
            $user->setEnabled(1);
            $user->setEmailPref($emailPref);

            $manager->persist($emailPref);
            $manager->persist($user);
            $manager->flush();
       }
       return "Success";
    }

    public function createNewAppointment($tutorUsername){
        /* Creates a new appointment */
        $manager = $this->getDoctrine()->getManager();
        $helper = $this->get('wchelper');
        $currentTime = date_create(date('Y-m-d H:i:s', time()));

        $newAppt = new WCAppointmentData();
        $newAppt->setStudUsername('');
        $newAppt->setTutorUsername($tutorUsername->getUsername());
        $newAppt->setProgram('CAS');
        $newAppt->setStartTime($currentTime);
        $newAppt->setStudentSignIn($currentTime);
        $newAppt->setActualStartTime($currentTime);
        $newAppt->setEndTime($currentTime);
        $newAppt->setStudentSignOut($currentTime);
        $newAppt->setCompletedTime($currentTime);
        $newAppt->setProfEmail('');
        $newAppt->setCheckIn('5'); //5 == WalkIn
        $newAppt->setRequestSub("No");
        $manager->persist($newAppt);
        return $newAppt;
    }

    public function getAppointment(){
        /* Gets the appointment that was last created. */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $tutorUsername = $helper->getCurrentUser();
        $currentDate = date_create(date('Y-m-d', time()));
        $currentTime = date_create(date('H:i:s', time()));
        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.TutorUsername LIKE :TutorUsername AND p.StartTime = :currentDate AND p.StartTime <= :currentTime AND p.CheckIn = :WalkIn")
          ->setParameters(array('TutorUsername' => '%'.$tutorUsername.'%', 'currentDate' => $currentDate, 'currentTime' => $currentTime, 'WalkIn' => 5))
          ->getQuery();

        $data =  $query->getResult();
        if(sizeof($data) == 0)
        {
            return new Response("There was an error with the request. Try again. If that doesn't work, contact ".$helper->getCMName().".");
        }

        return $data[sizeof($data)-1]; //Gets the most recent appointment with a student and a tutor.
    }

    public function createNewUser($studentUsername){
        $helper = $this->get('wchelper');
        $manager = $this->getDoctrine()->getManager();
        $isStudent = false; // A bool to check if user is a student.
        //
        $wsapi = $this->get('wsapi');
        $roles = $wsapi->getRoles($studentUsername);
        if( sizeof($roles) == 0){
            return new JsonResponse("Failed"); //the user has no roles.
        }
        $names = $wsapi->getNames($studentUsername);

        $firstName = $names["0"]["firstName"];
        $lastName = $names["0"]["lastName"];
        $prefFirstName = $names["0"]["prefFirstName"];

        if ($prefFirstName){
            $firstName = $prefFirstName;
        }

        foreach ($roles as $index => $role) {
            $role = $role['userRole'];
             // Determine if the user is a student.
            if($role == "STUDENT")
                $isStudent = true;
        }

        // Either create a new user if they are found through banner,
        //  Else return an error.
        if($isStudent){
            $user = new User();
            $emailPref = new WCEmailPreferences();
            $manager->persist($emailPref);

            $studentRole = $this->container->get('doctrine.orm.entity_manager')->getRepository('BethelUserBundle:Role')->findOneBy(array(
                            'role' => 'ROLE_STUDENT'
                            ));

            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setUsername($studentUsername);
            $user->setEmail($studentUsername.'@bethel.edu');
            $user->setPassword(NULL);
            $user->addRole($studentRole);
            $user->setEnabled(1);
            $user->setEmailPref($emailPref);
 
            $manager->persist($user);

            $helper->flushRepository();

            return "Success";
        }
        else{
            return "Failed";
        }
    }
 }