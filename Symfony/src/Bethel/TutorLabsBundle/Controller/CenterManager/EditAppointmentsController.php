<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class EditAppointmentsController extends Controller{

	public function editAppointmentsViewAction(){
        // pass over an array of courses in the db.
        $em = $this->container->get('doctrine')->getEntityManager();
        $query = $em->createQuery(
            'SELECT distinct c.CourseCode 
            FROM BethelTutorLabsBundle:WCAppointmentData c 
            WHERE c.CourseCode IS NOT NULL
            ORDER BY c.CourseCode'
        );
        $result = $query->getResult();

        // put courses into a better array.
        $courses = array();
        foreach ($result as $course){
            if( $course['CourseCode'] != 'other' )
                array_push($courses, $course['CourseCode']);
        }
        // manually put 'other' on the end
        array_push($courses, 'other');

        // put a list of tags into an array
        $tags = array();
        foreach ($result as $course){
            $lastChar = substr($course['CourseCode'], -1, 1);
            if( $course['CourseCode'] != 'other' && !is_numeric($lastChar) && !in_array($lastChar, $tags)){
                array_push($tags, $lastChar);
            }
        }
        sort($tags);

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_edit_appointments.html.twig', array( 
            'courses' => json_encode($courses),
            'tags'    => json_encode($tags)
        ));
    }

     //Returns student usernames in a select box.
    public function editAppointmentsGetStudentUsernamesAction(){
        $helper = $this->get('wchelper');
        return new Response($helper->getStudentUsernamesSelect());
    }

     //Returns tutor usernames in a select box.
    public function editAppointmentsGetTutorUsernamesAction(){
        $helper = $this->get('wchelper');
        return new Response($helper->getTutorUsernamesSelect());
    }

     //Returns prof usernames in a select box.
    public function editAppointmentsGetProfUsernamesAction(){
        $helper = $this->get('wchelper');
        return new Response($helper->getProfUsernamesSelect());
    }

//Return Search results
    public function editAppointmentsLoadAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        // get username from POST request
        $studentusername=$request->request->get('studentusername', '');
        $tutorusername=$request->request->get('tutorusername', '');
        $profusername=$request->request->get('profusername', '');
        $startdate=$request->request->get('startdate', '');
        $enddate=$request->request->get('enddate', '');
        $courses=$request->request->get('courses', '');

        if( $profusername == "No instructor")
            $profusername = "";
        if($startdate == "none")
            $startdate = null;
        if($enddate == "none")
            $enddate = null;
        if($courses == "none")
            $courses = null;

        $appointments = $helper->getAppointmentsWithCourses($studentusername, $tutorusername, $profusername, $startdate, $enddate, $courses);

        //Get first/last names.
        $BothNames = $helper->getApptUsersFirstNameLastName($appointments);
        $StudentNames = $BothNames[0];
        $TutorNames = $BothNames[1];

        $profNames = array();
        foreach($appointments as $appointment)
        {
            $profName = "";
            if( $appointment->getProfUsername() != "" || $appointment->getProfUsername() != null)
                $profName = $appointment->getProfUsername();
            array_push($profNames, $profName);
        }

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_edit_appointments_load.html.twig',
            array(
                'appointments'      => $appointments,
                'StudentNames'  => $StudentNames,
                'TutorNames'  => $TutorNames,
                'ProfNames'   => $profNames,
            )
        );
    }

//Bring up the edit menu
    public function editAppointmentsCallAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $ID=$request->request->get('ID', '');
         $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $ID)
            ->getQuery();
        $user =  $query->getResult();
        $user = $user[0];

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_edit_appointments_call.html.twig',
            array(
                    'user'      =>   $user
                )
            );
    }

//Delete
    public function deleteAppointmentsCallAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $ID=$request->request->get('ID', '');
         $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $ID)
            ->getQuery();
        $appointment =  $query->getResult();
        $appointment = $appointment[0];
        if (!$appointment){
            return new Response("hello!");
        }

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->remove($appointment);
        $entityManager->flush();

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_edit_appointments.html.twig');
    }

//Update
    public function updateAppointmentsCallAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        //All of the requests.
        $ID=$request->request->get('ID', '');
        $StudUsername=$request->request->get('StudUsername', '');
        $TutorUsername=$request->request->get('TutorUsername', '');
        $CourseCode=$request->request->get('CourseCode', '');
        $StartDate=$request->request->get('StartDate', '');
        $ActualStartDate=$request->request->get('ActualStartDate', '');
        $RequestSub=$request->request->get('RequestSub', '');
        $Multilingual=$request->request->get('Multilingual', '');
        $EndDate=$request->request->get('EndDate', '');
        $CompletedDate=$request->request->get('CompletedDate', '');
        $Program=$request->request->get('Program', '');
        $ProfEmail=$request->request->get('ProfEmail', '');
        $Assignment=$request->request->get('Assignment', '');
        $Comment=$request->request->get('Comment', '');
        $Suggestion=$request->request->get('Suggestion', '');
        $StartTime=$request->request->get('StartTime', '');
        $ActualStartTime=$request->request->get('ActualStartTime', '');
        $EndTime=$request->request->get('EndTime', '');
        $CompletedTime=$request->request->get('CompletedTime', '');

        //The query
        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $ID)
            ->getQuery();
        $user =  $query->getResult();
        $user = $user[0];

        //Checking to see if they have changed, if they have, set them.
        if($StudUsername != "")
            $user->setStudUsername($StudUsername);
        if($TutorUsername != "")
            $user->setTutorUsername($TutorUsername);
        if($CourseCode != "")
            $user->setCourseCode($CourseCode);
        if($RequestSub != "")
            $user->setRequestSub($RequestSub);
        if($Multilingual != "")
            $user->setMultilingual($Multilingual);
        if($Program != "")
            $user->setProgram($Program);
        if($ProfEmail != "")
            $user->setProfEmail($ProfEmail);
        if($Assignment != "")
            $user->setAssignment($Assignment);
        if($Comment != "")
            $user->setComment($Comment);
        if($Suggestion != "")
            $user->setSuggestion($Suggestion);


        //If the dates have changed, format them, then set them.
        if($StartTime != "" || $StartDate != ""){
            $oldStart = $user->getStartTime();
            $newStart = $helper->getNewTimeFormat($oldStart, $StartDate, $StartTime);
            $user->setStartTime($newStart);
        }

        if($ActualStartTime != "" || $ActualStartDate != ""){
            $oldStart = $user->getActualStartTime();
            $newStart = $helper->getNewTimeFormat($oldStart, $ActualStartDate, $ActualStartTime);
            $user->setActualStartTime($newStart);
        }

        if($EndTime != "" || $EndDate != ""){
            $oldEnd = $user->getEndTime();
            $newEnd = $helper->getNewTimeFormat($oldEnd, $EndDate, $EndTime);
            $user->setEndTime($newEnd);
        }

        if($CompletedTime != "" || $CompletedDate != ""){
            $oldCompleted = $user->getCompletedTime();
            $newCompleted = $helper->getNewTimeFormat($oldCompleted, $CompletedDate, $CompletedTime);
            $user->setCompletedTime($newCompleted);
        }

        $helper->flushRepository();
        return $this->render('BethelTutorLabsBundle:CenterManager:cm_edit_appointments_call.html.twig',
            array(
                    'user'      =>   $user
                )
        );
    }
}