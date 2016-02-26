<?php

namespace Bethel\TutorLabsBundle\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class TutorReportsController extends Controller{

	public function reportsViewAction(){
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

        return $this->render('BethelTutorLabsBundle:Tutor:tutor_reports.html.twig', array( 'courses' => json_encode($courses) ));
    }

    public function reportsCallAction(){
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

        return $this->render('BethelTutorLabsBundle:Tutor:tutor_reports_call.html.twig',
            array(
                'appointments'      => $appointments,
                'StudentNames'  => $StudentNames,
                'TutorNames'  => $TutorNames,
                'ProfNames'   => $profNames,
            )
        );
    }

    //Returns student usernames in a select box.
    public function reportsGetStudentUsernamesAction(){
        $helper = $this->get('wchelper');
        return new Response($helper->getStudentUsernamesSelect());
    }

    //Returns Tutor usernames in a select box.
    public function reportsGetTutorUsernamesAction(){
        $helper = $this->get('wchelper');
        return new Response($helper->getTutorUsernamesSelect());
    }

     //Returns prof usernames in a select box.
    public function reportsGetProfUsernamesAction(){
        $helper = $this->get('wchelper');
        return new Response($helper->getProfUsernamesSelect());
    }
}