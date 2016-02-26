<?php

namespace Bethel\TutorLabsBundle\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ScheduleController extends Controller{

    public function scheduleViewAction(){
        $helper = $this->get('wchelper');
        $username = $helper->getCurrentUser()->getUsername();
        $tutors = $helper->getAllTutors();

        // Sort Tutors
        $newTutors = array();
        $newTutors = $tutors;
        usort($newTutors, function($a, $b){
            return strnatcmp ($a->getFirstName(), $b->getFirstName());
        });

        /* Standard view function */
      return $this->render('BethelTutorLabsBundle:Tutor:tutor_schedule.html.twig', array('tutors' => $newTutors, 'user'=>$username ));
    }

    public function scheduleLoadAction(){
        /* This takes in a given username and returns the shifts that the user has after today. */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $repository = $helper->getWCAppointmentRepository();
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

    public function requestSubAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $username = $helper->getCurrentUser()->getUsername();
        // get appointmentID from POST request
        $appointmentID = intval($request->request->get('appointmentID', ''));

        $repository = $helper->getWCAppointmentRepository();

        //get correct appointment
        $appointmentData = $repository->findById($appointmentID);

        $appointmentData = $appointmentData[0]; // Index should be 0 because there is only one element that has ID, appointmentID.
        if($appointmentData->getRequestSub() == "Yes")
            return new Response("Already Requested a substitute");
        $appointmentData->setRequestSub("Yes");
        $helper->flushRepository();
        
        $test = $helper->sendTutorsSubRequestEmails($appointmentData);
        
        return new Response("Success");
    }    

    public function removeSubAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get appointmentID from POST request
        $appointmentID = intval($request->request->get('appointmentID', ''));

        $repository = $helper->getWCAppointmentRepository();

        //get correct appointment
        $appointmentData = $repository->findById($appointmentID);

        $appointmentData = $appointmentData[0]; //Index should be 0 because there is only one element that has ID, appointmentID.

        $appointmentData->setRequestSub("No");
        $helper->flushRepository();

        return new Response("Success");
    }    
}