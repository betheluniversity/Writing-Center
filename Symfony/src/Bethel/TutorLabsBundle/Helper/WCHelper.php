<?php
// src/Bethel/TutorLabsBundle/Helper/TutorLabsHelper.php

namespace Bethel\TutorLabsBundle\Helper;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DateTime;

class WCHelper extends Controller{

    public function __construct($container = null){
      // See this URL for an explanation of why this is here
      // http://stackoverflow.com/a/12905319
      $this->container = $container;
    }

    public function getRequest(){
        /* Get the request */
        $request = $this->container->get('request');
        return $request;
    }

    public function getUserRepository(){
        /* The User Repository */
        return $this->getDoctrine()->getRepository('BethelUserBundle:User');
    }

    public function getWCStudentBansRepository(){
        /* The Student Bans Repository */
        return $this->getDoctrine()->getRepository('BethelTutorLabsBundle:WCStudentBans');
    }

    public function getWCSystemSettingsRepository(){
        /* The System Settings Repository */
      return $this->getDoctrine()->getRepository('BethelTutorLabsBundle:WCSystemSettings');
    }

    public function getWCAppointmentRepository(){
      /* The Appointment Repository */
      return $this->getDoctrine()->getRepository('BethelTutorLabsBundle:WCAppointmentData');
    }

    public function getWCScheduleRepository(){
      /* The LabSchedule Repository */
      return $this->getDoctrine()->getRepository('BethelTutorLabsBundle:WCSchedule');
    }

    public function getWCEmailPreferencesRepository(){
      /* The Email Preferences Repository */
      return $this->getDoctrine()->getRepository('BethelTutorLabsBundle:WCEmailPreferences');
    }

    public function getWCRolesRepository(){
      /* The User Roles Repository */
      return $this->getDoctrine()->getRepository('BethelUserBundle:Role');
    }

    public function flushRepository(){
      /* A command to flush the repository */
        return  $this->getDoctrine()->getManager()->flush();
    }

    public function getUsersWithUsernameLike($username){
      /* Finds all users with a username like $username*/

      // get repository
      $repository = $this->getUserRepository();

      // Find usernames
      $query = $repository->createQueryBuilder('p')
          ->where('p.username LIKE :username')
          // !! %username% will return anything that containers $username
          // !! So %843% will return both ejc84332, ejc84333 and abc84332.
          ->setParameter('username', '%' . $username . '%')
          ->getQuery();

      // Store the results in an array
      return $query->getResult();
    }

    public function getAllStudentBans(){
      $existing = $this->getWCStudentBansRepository()->findAll();
      return existing;
    }

    //Same as getUsersWithUsernameLike but only searches students
    public function getStudentsWithUsernameLike($username){
      /* Finds all users with a username like $username*/

      // get repository
      $repository = $this->getUserRepository();
      $user = $repository->loadUserByUsername($username);
      return $user;
    }

    public function getUnbannedStudentsWithUsernameFirstNameLastName($username, $firstName, $lastName){
      /* Finds all students with a username/firstname/lastname */

      // get repository
        /** @var $repository \Bethel\UserBundle\Entity\UserRepository */
        $repository = $this->getUserRepository();

        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->container->get('doctrine')->getEntityManager();
        $query = $em->createQuery(
            'SELECT u
            FROM BethelUserBundle:User u
            JOIN u.roles r
            WHERE r.role = :role
            AND u.username LIKE :username
            AND u.firstName LIKE :firstName
            AND u.lastName LIKE :lastName
            AND u.id NOT IN (SELECT IDENTITY(b.user) FROM BethelTutorLabsBundle:WCStudentBans b)'
        )
            ->setParameters(
            array(
              "username"=> '%'.$username.'%',
              "firstName"=> '%'.$firstName.'%',
              "lastName"=> '%'.$lastName.'%',
              "role" => 'ROLE_STUDENT'
              )
            );

      // Store the results in an array
      return $query->getResult();
    }

    public function getStudentsWithUsernameFirstNameLastName($username, $firstName, $lastName){
      /* Finds all students with a username/firstname/lastname */

      // get repository
      $repository = $this->getUserRepository();
      $query = $repository->createQueryBuilder('p')
        ->innerJoin('p.roles','s','WITH','s.role = :role')
        ->where('p.username LIKE :username')
        ->andWhere('p.firstName LIKE :firstName')
        ->andWhere('p.lastName LIKE :lastName')
        ->setParameters(
            array(
              "username"=> '%'.$username.'%', 
              "firstName"=> '%'.$firstName.'%', 
              "lastName"=> '%'.$lastName.'%', 
              'role' => '%ROLE_STUDENT%'
              )
            )
        ->getQuery();

      // Store the results in an array
      return $query->getResult();
    }

    public function getUsersWithUsernameFirstNameLastName($username, $firstName, $lastName){
      /* Finds all users with a username/firstname/lastname */
      // get repository
      $repository = $this->getUserRepository();
      $query = $repository->createQueryBuilder('p')
        ->where('p.username LIKE :username AND p.firstName LIKE :firstName AND p.lastName LIKE :lastName')
        ->setParameters(array("username"=> '%'.$username.'%', "firstName"=> '%'.$firstName.'%', "lastName"=> '%'.$lastName.'%'))
        ->getQuery();

      // Store the results in an array
      return $query->getResult();
    }

    public function getUsersWithFirstNameLastName($firstName, $lastName){
      /* Finds all users with a username/firstname/lastname */

      $repository = $this->getUserRepository();
      $query = $repository->createQueryBuilder('p')
        ->where('p.firstName LIKE :firstName AND p.lastName LIKE :lastName')
        ->setParameters(array("firstName"=> '%'.$firstName.'%', "lastName"=> '%'.$lastName.'%'))
        ->getQuery();

      // Store the results in an array
      return $query->getResult();
    }

    public function getTutorsWithFirstNameLastName($firstName, $lastName){
      /* Finds all users with a username/firstname/lastname */

      $repository = $this->getUserRepository();
      $query = $repository->createQueryBuilder('p')
        ->where('p.firstName LIKE :firstName AND p.lastName LIKE :lastName')
        ->setParameters(array("firstName"=> '%'.$firstName.'%', "lastName"=> '%'.$lastName.'%'))
        ->getQuery();

      // Store the results in an array
      return $query->getResult();
    }

    public function getBannedUsersFirstNameLastName($data){
      $Names = array();
        $counter = 0;
        foreach($data as $user){
            $Names[$counter] = $this->getUserRepository()->findOneByUsername($user->getUsername());
            $counter++;     
        }
        return $Names;
    }

    public function getApptUsersFirstNameLastName($appointments){
      $helper = $this->get('wchelper');
      $StudentNames = array();
      $TutorNames = array();
        $counter = 0;

        foreach($appointments as $appointment){
            $studentname = $this->getUserRepository()->findOneByUsername($appointment->getStudUsername());
            if($studentname == "")
              $StudentNames[$counter] = "NO STUDENT";
            else
              $StudentNames[$counter] = $studentname;

            $tutorname = $this->getUserRepository()->findOneByUsername($appointment->getTutorUsername());
            if($tutorname == "")
              $TutorNames[$counter] = "NO TUTOR";
            else
              $TutorNames[$counter] = $tutorname;
            $counter++;         
        }
        $bothArrays = array($StudentNames, $TutorNames);
        return $bothArrays;
    }

    public function setUserRole($username, $roleName){
      // Gives $username role $role
      $userRepository = $this->getUserRepository();
      $user = $userRepository->findByUsername($username);
      //$user is now an array, with $user[0] being the Doctrine object we want.

      $user = $user[0];

      $roleRepository = $this->getWCRolesRepository();
      $role = $roleRepository->getRoleByName($roleName);
      
      $user->setRoles($role);
      $this->flushRepository();
    }

    public function sendMessage($email, $subject, $body){
      /* Sends a message */
        $message = \Swift_Message::newInstance()
          ->setSubject($subject)
          ->setFrom("no-reply@bethel.edu")
          ->setTo($email)
          ->setBody(
              $body
          );
        $this->get('mailer')->send($message);
    }

    public function getAppointmentsWith($StudUsername, $TutorUsername, $ProfUsername, $startdate, $enddate){
      /* Finds all appointments matching parameters */

      if( $ProfUsername == "No instructor")
        $ProfUsername = "";
      else
        $ProfUsername = "%".$ProfUsername."%";

      // get repository
      $repository = $this->getWCAppointmentRepository();

      if($startdate != null)
      {
        $startdate = date_create($startdate);
        $startdate = date_format($startdate, 'Y-m-d 00:i:s');
      }
      if($enddate != null)
      {
        $enddate = date_create($enddate);
        $enddate = date_format($enddate, 'Y-m-d 00:i:s');
      }

      if($enddate != null && $startdate != null){
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime >= :start AND p.CompletedTime <= :end')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' =>  $ProfUsername ,'start' => $startdate, 'end' => $enddate))
        ->getQuery();
      }
      elseif ($enddate != null && $startdate == null) {
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime <= :end')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername , 'end' => $enddate))
        ->getQuery();
      }
      elseif ($enddate == null && $startdate != null) {
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime >= :start')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername , 'start' => $startdate))
        ->getQuery();
      }
      else{
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername ))
        ->getQuery();
      }
      $data = $query->getResult();

      return $data;
    }

    // Todo: clean this up. It was a quick fix on top of a quick fix.
    public function getAppointmentsWithCourses($StudUsername, $TutorUsername, $ProfUsername, $startdate, $enddate, $courses){
      /* Finds all appointments matching parameters */

      if( $ProfUsername == "No instructor")
        $ProfUsername = "";
      else
        $ProfUsername = "%".$ProfUsername."%";

      // get repository
      $repository = $this->getWCAppointmentRepository();

      if($startdate != null)
      {
        $startdate = date_create($startdate);
        $startdate = date_format($startdate, 'Y-m-d 00:i:s');
      }
      if($enddate != null)
      {
        $enddate = date_create($enddate);
        $enddate = date_format($enddate, 'Y-m-d 00:i:s');
      }

      // if it is a tag, add regex.
      if( $courses != null && strlen($courses) == 1) {
          // if it has numbers at somepoint before it.
          $courses = '[0-9]+[A-Z]*'.$courses;
      }

      if($enddate != null && $startdate != null && $courses != null){
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime >= :start AND p.CompletedTime <= :end AND p.CourseCode LIKE :courses')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' =>  $ProfUsername ,'start' => $startdate, 'end' => $enddate, 'courses' => $courses))
        ->distinct()
        ->getQuery();
      }
      elseif ($enddate != null && $startdate == null && $courses != null) {
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime <= :end AND p.CourseCode LIKE :courses')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername , 'end' => $enddate, 'courses' => $courses))
        ->getQuery();
      }
      elseif ($enddate == null && $startdate != null && $courses != null) {
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime >= :start AND p.CourseCode LIKE :courses')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername , 'start' => $startdate, 'courses' => $courses))
        ->getQuery();
      }
      elseif ( $enddate == null && $startdate == null && $courses != null){
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND REGEXP(p.CourseCode, :courses) = true')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername, 'courses' => $courses ))
        ->getQuery();
      }
      elseif($enddate != null && $startdate != null && $courses == null){
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime >= :start AND p.CompletedTime <= :end')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' =>  $ProfUsername ,'start' => $startdate, 'end' => $enddate))
        ->getQuery();
      }
      elseif ($enddate != null && $startdate == null && $courses == null) {
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime <= :end')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername , 'end' => $enddate))
        ->getQuery();
      }
      elseif ($enddate == null && $startdate != null && $courses == null) {
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername AND p.CompletedTime >= :start')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername , 'start' => $startdate))
        ->getQuery();
      }
      elseif ( $enddate == null && $startdate == null && $courses == null){
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.ProfUsername LIKE :profusername')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'profusername' => $ProfUsername ))
        ->getQuery();
      }
      $data = $query->getResult();

      return $data;
    }
    
    public function getAppointmentsWithNoShow($StudUsername, $TutorUsername, $startdate, $enddate, $CheckIn){
      /* Finds all appointments matching parameters */

      // get repository
      $repository = $this->getWCAppointmentRepository();

      if($startdate != null)
      {
        $startdate = date_create($startdate);
        $startdate = date_format($startdate, 'Y-m-d 00:i:s');
      }
      if($enddate != null)
      {
        $enddate = date_create($enddate);
        $enddate = date_format($enddate, 'Y-m-d 00:i:s');
      }

      if($enddate != null && $startdate != null){
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.CompletedTime >= :start AND p.CompletedTime <= :end AND p.CheckIn = :checkin')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'start' => $startdate, 'end' => $enddate, 'checkin' => 4)) //4 for no show.
        ->getQuery();
      }
      elseif ($enddate != null && $startdate == null) {
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.CompletedTime <= :end AND p.CheckIn = :checkin')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'end' => $enddate, 'checkin' => 4)) //4 for no show.
        ->getQuery();
      }
      elseif ($enddate == null && $startdate != null) {
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.CompletedTime >= :start AND p.CheckIn = :checkin')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'start' => $startdate, 'checkin' => 4)) //4 for no show.
        ->getQuery();
      }
      else{
        $query = $repository->createQueryBuilder('p')
        ->where('p.StudUsername LIKE :username AND p.TutorUsername LIKE :tutorusername AND p.CheckIn = :checkin')
        ->setParameters(array('username' => '%' . $StudUsername . '%', 'tutorusername' => '%' . $TutorUsername . '%', 'checkin' => 4)) //4 for no show.
        ->getQuery();
      }

      return $query->getResult();
    }
    
    public function getAvailableTimeslots(){
      /* Get all time slots that are active. */
        $repository = $this->getWCScheduleRepository();

        // find all times where isActive == 'yes'
        $query = $repository->createQueryBuilder('p')
          ->where("p.isActive = 'yes'")
          ->getQuery();
        return $query->getResult();
    }

    public function getAllTutors(){
      /* Get all users with the role, tutor. */
      $repository = $this->getUserRepository();

      return $repository->getUserByRole("ROLE_TUTOR");
      
    }

    public function getAllCMs(){
      /* Get all users with the role, tutor. */
      $repository = $this->getUserRepository();

      return $repository->getUserByRole("ROLE_CENTER_MANAGER");
    }

    public function getAllStudents(){
      /* Get all users with the role, student. */
      $repository = $this->getUserRepository();

      $students = $repository->getUserByRole("ROLE_STUDENT");
      return $students;
    }

    public function dayToNumber($day){
      /* Converts a string to a number to be sorted properly */
      if($day == "Sunday")
        return 1;
      elseif($day == "Monday")
        return 2;
      elseif($day == "Tuesday")
        return 3;
      elseif($day == "Wednesday")
        return 4;
      elseif($day == "Thursday")
        return 5;
      elseif($day == "Friday")
        return 6;
      else
        return 7;
    }

    public function getSlotsForDay($day){
      /* This should be given a day, and return an array of each timeslot in LabScheduleRepository that is on that day */
      $repository = $this->getWCScheduleRepository();

      $query = $repository->createQueryBuilder('p')
          ->where("p.dayOfWeek = $day")
          ->getQuery();
        return $query->getResult();
    }


    // Returns the Tutors looking for subs
    public function getSubstituteData() {
        $repository = $this->getWCAppointmentRepository();
        $Test = "Yes"; //change this name.
        $query = $repository->createQueryBuilder('p')
          ->where("p.RequestSub = :Test")
          ->setParameter('Test', $Test)
          ->getQuery();

        $data = $query->getResult();
        
        return $data;
    }

    //Returns the new time in the right format
    //Changes 'Y-m-d g:i A' to 'Y-m-d H:i:s'
    public function getNewTimeFormat($oldStart, $StartDate, $StartTime) {
            $oldStart = $oldStart->format('Y-m-d H:i:s');
            list($oldStartDate, $oldStartTime) = explode(' ', $oldStart);
            $newStartDate;
            $newStartTime;
            $newStart;
            if($StartDate == "")
                $newStartDate = $oldStartDate;
            else
                $newStartDate = $StartDate;
            if($StartTime == "")
                $newStartTime = $oldStartTime;
            else{
                list($startHour, $part2) = explode(':', $StartTime);
                list($startMinute, $startPeriod) = explode(' ', $part2);
                if ($startPeriod == "pm" || $startPeriod == "PM"){
                    $startHour = intval($startHour) + 12;
                }
                $newStartTime = $startHour.':'.$startMinute.':00';
            }
            
            $newStart = $newStartDate.' '.$newStartTime;
            $newStart = date_create($newStart);
            date_format($newStart, 'Y-m-d H:i:s');
            return $newStart;
    }

    // gets converts appointments to a format that can be read by full calendar
    public function getAppointmentsAsJSON($appointments) {

        $json_array = array();

        foreach ($appointments as $appointment) {
            //Get tutor first/last name.
            $tutorname = $this->getUsersWithUsernameLike($appointment->getTutorUsername());
            $tutorname = $tutorname[0]->getFirstName()." ".$tutorname[0]->getLastName();

            $noShow;
            if($appointment->getCheckIn() == "4")
              $noShow = "You did not show up to this appointment";
            else
              $noShow = "";

            //Title and Multilingual.
            $title = $appointment->getStartTime()->format("g:ia")." - ".$appointment->getEndTime()->format("g:ia")." ".$tutorname;
            
            $color = '#4169E1';
            $multilingualTitle = "";
            if($appointment->getMultilingual()){
              $title = "M ".$title;
              $multilingualTitle = "Multilingual";
              $color = '#D4AF37';
            }

            if( $appointment->getDropInAppt() ){
              $title = $title . " | Drop-in Hours";
              $color = "green";
            }

            //Student sign in/ sign out times.
            if($appointment->getStudentSignIn() == null)
              $studentSignIn = "no";
            else
              $studentSignIn = $appointment->getStudentSignIn()->format("m/d/Y g:i a");
            if($appointment->getStudentSignOut() == null)
              $studentSignOut = "no";
            else
              $studentSignOut = $appointment->getStudentSignOut()->format("m/d/Y g:i a");

            //Completed Time.
            $completedTime = "";
            if($appointment->getCompletedTime() != null)
              $appointment->getCompletedTime()->format("m/d/Y g:i a");

            //Creates the array.
            $event_array = array(
                "title"             => $title,
                "student"           => $appointment->getStudUsername(),
                "studentname"       => $this->getFirstLastNameByUsername( $appointment->getStudUsername() ),
                "tutor"             => $appointment->getTutorUsername(),
                "tutorname"         => $tutorname,
                "sub"               => $appointment->getRequestSub(),
                "noshow"            => $noShow,
                "assignment"        => $appointment->getAssignment(),
                "comment"           => $appointment->getComment(),
                "suggestion"        => $appointment->getSuggestion(),
                "start"             => $appointment->getStartTime()->format("m/d/Y g:i a"),
                "end"               => $appointment->getEndTime()->format("m/d/Y g:i a"),
                "completed"         => $completedTime,
                "allDay"            => false,
                "appointmentID"     => $appointment->getId(),
                "multilingual"      => $appointment->getMultilingual(),
                "multilingualTitle" => $multilingualTitle,
                "studentSignIn"     => $studentSignIn,
                "studentSignOut"    => $studentSignOut,
                "color"             => $color,
                "dropin"            => $appointment->getDropInAppt(),
              );
            array_push($json_array, $event_array);
        }

        $data = json_encode($json_array);
        return $data;
    }

    // takes in a json string and returns a json response
    public function makeJSONResponse($json) {
        $response = new Response();
        $response->setContent($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    // Gets the current username just like {{app.user.username}} in twig
    //  ( borrowed from https://groups.google.com/forum/#!topic/symfony2/axTKcWWpghI )
    public function getCurrentUser() {
      return $this->container->get('security.context')->getToken()->getUser();
    }

    //Takes in a username and returns email preferences as an object.
    public function findEmailPrefByUsername($username)
    {
        $repository = $this->getUserRepository();

        $query = $repository->createQueryBuilder('p')
          ->where("p.username LIKE :username")
          ->setParameter('username', '%'.$username.'%')
          ->getQuery();

        $user = $query->getResult();
        $user = $user[0];

        $email_pref_id = $user->getEmailPref();

        $repository = $this->getWCEmailPreferencesRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.id = :email_pref_id")
          ->setParameter('email_pref_id', $email_pref_id)
          ->getQuery();

        $data = $query->getResult();

        if(sizeof($data) == 0)
          return "Email preferences for this user are not set up.";
        else
          return $data[0];
    }

    public function sendTutorsSubRequestEmails($appointmentData){
        // Temporarily change the array so that each has a first/last name.

        $tutorUsername = $this->getFirstLastNameByUsername( $appointmentData->getTutorUsername() );
        
        $subject = "Writing Center: New Substitute Request";
        $body = $tutorUsername." is requesting a substitute. Go onto the Writing Center website to view the substitute request if you would like to sub for ".$tutorUsername. ". Thank you.\n\n".
                "Date: ".$appointmentData->getStartTime()->format("m/d/Y")."\n".
                "Time: ".$appointmentData->getStartTime()->format("g:i a")." - ".$appointmentData->getEndTime()->format("g:i a");
        
        $tutors = $this->getAllTutors();
        $cms = $this->getAllCMs();
        $usersArray = array_merge($tutors, $cms);

        foreach($usersArray as $user){
          $email = $user->getEmail();
          $username = $user->getUsername();

          //Don't email the tutor who requested a sub.
          if($username == $tutorUsername)
              continue;
          // Only email the tutors who have the preference turned on.
          $email_pref = $this->findEmailPrefByUsername($username);
          if($email_pref->getSubRequestEmail())
            $this->sendMessage($email, $subject, $body);
        }

    }

    public function getInstructorStudents($InstructorUsername){
      $wsapi = $this->get('wsapi');

        $students = $wsapi->getStudents($InstructorUsername);

        $users = array(); //creates a array of the student's usernames
        $profStudents = array();
        foreach($students as $student){
            if(in_array($student['username'], $users) == false){

                $repository = $this->getUserRepository();
                $query = $repository->createQueryBuilder('p')
                    ->where('p.username LIKE :username')
                    ->setParameter('username', '%'.$student['username'].'%')
                    ->getQuery();
                $data = $query->getResult();

                if(sizeof($data) == 0)
                    continue;
                array_push($profStudents, $data[0]); //actual array of students.
                array_push($users, $student['username']); //is a dummy array to hold the names of already added students.
            }
        }
        return $profStudents;
    }

    //Returns the Center Manager's name. Should be dynamic to get the current CM's name.
    public function getCMName(){
        // $repository = $this->getUserRepository();
        // $query = $repository->createQueryBuilder('p')
        //     ->where('p.Role LIKE :Role')
        //     ->setParameter('Role', '%'.'CENTER_MANAGER'.'%')
        //     ->getQuery();
        // $data = $query->getResult();
        //if(sizeof($data) == 0)
          return "April Schmidt";
        //return $data[0]->getFirstName()." ".$data[0]->getLastName();

    }

    public function emailTuteeAndInstructor($profEmail, $tuteeEmail, $newAppt, $ferpaEmail){
      //Put the data from $newAppt into the body.
        $subject = "TutorLabs - Completed Appointment";

        $StudentUsername = "Student: ".$this->getFirstLastNameByUsername( $newAppt->getStudUsername() )."   \n";
        $TutorUsername = "Tutor: ". $this->getFirstLastNameByUsername( $newAppt->getTutorUsername() )."   \n";
        $CourseCode = "Course Code: ".$newAppt->getCourseCode()."   \n";
        if( $newAppt->getMultilingual() != 0)
            $Multilingual = "Multilingual: ".$newAppt->getMultilingual()."   \n";
        else
            $Multilingual = "";
        $Assignment = "Assignment: ".$newAppt->getAssignment()."   \n\n";
        $Comments = "Worked on: ".$newAppt->getComment()."   \n\n";
        $Suggestion = "Suggestions: ".$newAppt->getSuggestion()."   \n\n";

        $data = $StudentUsername.$TutorUsername.$CourseCode.$Multilingual.$Assignment.$Comments.$Suggestion;
        $profBody = "You are receiving this message because one of your students has attended the Writing Center. Here are the details:  \n\n".$data;
        $tutorBody = "You are receiving this message because you attended an appointment. Here are the details:  \n\n".$data;
        
        if($profEmail != "Failed" && $profEmail != "" && $ferpaEmail == 1)
          $this->sendMessage($profEmail, $subject, $profBody);
        if( $newAppt->getStudUsername() != NULL || $newAppt->getStudUsername() != "")
          $this->sendMessage($tuteeEmail, $subject, $tutorBody);
    }

    public function getProfEmail($studentUsername, $CourseCode){
      $wsapi = $this->get('wsapi');
      $courses = $wsapi->getCourses($studentUsername);
      $profUsername;
      foreach($courses as $course => $info){
        if($info['subject'].$info['cNumber'] == $CourseCode){
            $profUsername = $info['instructorUsername'];
            return $profUsername."@bethel.edu";
        }
      }
      //else
      return "Failed";
    }

    public function getProfName($studentUsername, $CourseCode){
      $wsapi = $this->get('wsapi');
      $courses = $wsapi->getCourses($studentUsername);
      $profUsername = "";
      foreach($courses as $course => $info){
        if($info['subject'].$info['cNumber'] == $CourseCode){
            $profUsername = $info['instructor'];
            return $profUsername;
        }
      }
      //else
      return $profUsername;
    }

    // This is wrong....it actually is returning the prof name, instead of the prof username. 
    // When there is time, find all uses of this and fix it.
    public function getProfUsername($studentUsername, $CourseCode){
      $wsapi = $this->get('wsapi');
      $courses = $wsapi->getCourses($studentUsername);
      foreach($courses as $course => $info){
        if($info['subject'].$info['cNumber'] == $CourseCode){
            return $info['instructor'];
        }
      }
      //else
      return "";
    }

    public function checkIfScheduleEmpty(){
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
        if(sizeof($data) == 0)
          return "EMPTY";
        return "NOT EMPTY";
    }

    /////////////////////Functions to get first/last names in a select box.//////////////
    public function getStudentUsernamesSelect(){
        $request = $this->getRequest();

        $studentfirstname=$request->request->get('studentfirstname', '');
        $studentlastname=$request->request->get('studentlastname', '');

        $students = $this->getUsersWithFirstNameLastName($studentfirstname, $studentlastname);

        foreach ($students as $student) {
          if($student->hasRole("ROLE_STUDENT") || $student->hasRole("ROLE_TUTOR"))
          {
            //these are the ones we want to keep, it's a bit ugly :/
          }
          else //remove non-students
          {
            $index = array_search($student, $students);
            unset($students[$index]);
          }
        }

        usort($students, array( $this, "sort_user_array_by_names"));

        $select = "<select class='form-control' id='select-student-username' size='5'>";
            foreach ($students as $student) {
                $studentUsername = $student->getUsername();
                $studentName = $student->getFirstName()." ".$student->getLastName()." | '".$studentUsername."'";
                $select .= "<option value='".$studentUsername."'>".$studentName."</option>";
            }
        $select .= "</select>";

        return $select;
    }

    function sort_user_array_by_names($a, $b)
      {
          $a = $a->getFirstName()." ".$a->getLastName()." | '".$a->getUsername()."'";
          $b = $b->getFirstName()." ".$b->getLastName()." | '".$b->getUsername()."'";
          return strcmp($a, $b);
      }

    public function getTutorUsernamesSelect(){
        $request = $this->getRequest();

        $tutorfirstname=$request->request->get('tutorfirstname', '');
        $tutorlastname=$request->request->get('tutorlastname', '');

        $tutors = $this->getTutorsWithFirstNameLastName($tutorfirstname, $tutorlastname);

        foreach ($tutors as $tutor) {
          if($tutor->hasRole("ROLE_TUTOR") || $tutor->hasRole("ROLE_CENTER_MANAGER"))
          {
            //these are the ones we want to keep, it's a bit ugly :/
          }
          else //remove non-tutors and non-cm's
          {
            $index = array_search($tutor, $tutors);
            unset($tutors[$index]);
          }
        }

        usort($tutors, array( $this, "sort_user_array_by_names"));
        

        $select = "<select id='select-tutor-username' class='form-control' size='5'>";
            foreach ($tutors as $tutor) {
                $tutorUsername = $tutor->getUsername();
                $tutorName = $tutor->getFirstName()." ".$tutor->getLastName()." | '".$tutorUsername."'";
                $select .= "<option value='".$tutorUsername."'>".$tutorName."</option>";
            }
        $select .= "</select>";

        return $select;
    }

    //PROF
    public function getProfUsernamesSelect(){
        $request = $this->getRequest();
        $helper = $this->get('wchelper');

        $proffirstname=$request->request->get('proffirstname', '');
        $proflastname=$request->request->get('proflastname', '');

        //loop through ALL appts. return ProfUsername
        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->findAll();

        $arrayOfUniqueProfUsernames = array();
        foreach( $query as $appointment)
        {
          $newProfUsername = $appointment->getProfUsername();
          if( !in_array($newProfUsername, $arrayOfUniqueProfUsernames) && $newProfUsername != "" && $newProfUsername != NULL )
          {
              array_push($arrayOfUniqueProfUsernames, $newProfUsername);
          }
        }

        sort($arrayOfUniqueProfUsernames);

        $select = "<select id='select-prof-username' class='form-control' size='5'>";
        $select .= "<option value='No instructor'>No instructor</option>";
            foreach ($arrayOfUniqueProfUsernames as $profUsername) {
                // $profUsername = $prof->getUsername();
                // $profName = $prof->getFirstName()." ".$prof->getLastName()." | '".$profUsername."'";
                $select .= "<option value='".$profUsername."'>".$profUsername."</option>";
            }
        $select .= "</select>";

        return $select;
    }

    /////////////////////End first/last name functions/////////////////////////////////////////

    ///////////////////// API functions //////////////////////
    public function getAppointmentsWithStudent($studentUsername){
          $repository = $this->getWCAppointmentRepository();
          $query = $repository->createQueryBuilder('p')
              ->where("p.StudUsername LIKE :StudUsername")
              ->setParameter('StudUsername', '%'.$studentUsername.'%')
              ->getQuery();
   
          return $query->getResult();
    }

    public function getAppointmentsWithTutor($tutorUsername){
          $repository = $this->getWCAppointmentRepository();
          $query = $repository->createQueryBuilder('p')
              ->where("p.TutorUsername LIKE :TutorUsername")
              ->setParameter('TutorUsername', '%'.$tutorUsername.'%')
              ->getQuery();
   
          return $query->getResult();
    }

//Not currently in use. Can be deleted.
    // public function getAppointmentsInDateRange($start, $end){
    //       $repository = $this->getWCAppointmentRepository();
    //       $query = $repository->createQueryBuilder('p')
    //           ->where("p.StartTime >= :StartTime AND p.EndTime <= :EndTime")
    //           ->setParameters(array('StartTime' => $start, 'EndTime' => $end))
    //           ->getQuery();
   
    //       return $query->getResult();
    // }


    
    public function getFirstLastNameByUsername( $username){
      if( $username != "")
      {
        $user = $this->getUserRepository()->loadUserByUsername( $username );
        return $user->getFirstName() . " " . $user->getLastName();
      }
      else
        return "";
    }

    // Make sure to not save to the DB after this. This is just for looks.
    public function quickFixStudentTutorUsernames( $data ){
      foreach( $data as $appt)
        {
            if( $appt->getStudUsername() != "")
            {
                $student = $this->getUserRepository()->loadUserByUsername($appt->getStudUsername());
                $appt->setStudUsername($student->getFirstName() . " " . $student->getLastName());
            }
            else
                $appt->setStudUsername(" ");

            if( $appt->getTutorUsername() != "")
            {
                $tutor = $this->getUserRepository()->loadUserByUsername($appt->getTutorUsername());
            $appt->setTutorUsername($tutor->getFirstName() . " " . $tutor->getLastName());
            }
            else
                $appt->setTutorUsername(" ");
            
        }
        return $data;
    }


    ///////////////////// End API functions //////////////////////

} // end class
