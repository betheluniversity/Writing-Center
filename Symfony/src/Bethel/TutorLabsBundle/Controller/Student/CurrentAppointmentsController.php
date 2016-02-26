<?php

namespace Bethel\TutorLabsBundle\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CurrentAppointmentsController extends Controller{

	public function appointmentsViewAction(){
		return $this->render('BethelTutorLabsBundle:Student:student_current_appointments.html.twig');
	}
	public function appointmentsLoadAction(){
		/* This takes in a given username and returns the appointments the student has */
		$helper = $this->get('wchelper');
		$request = $helper->getRequest();
		$repository = $helper->getWCAppointmentRepository();
		$username=$helper->getCurrentUser()->getUsername();
		$today = date('Y-m-d', time());

		$query = $repository->createQueryBuilder('p')
			->where('p.StudUsername LIKE :username AND p.StartTime >= :todaysDate')
			->setParameters(array('username' => '%'.$username.'%', 'todaysDate' => $today))
			->getQuery();
			
		$data = $query->getResult();
        $json = $helper->getAppointmentsAsJSON($data);
    	return $helper->makeJSONResponse($json);
	}
	
	public function appointmentsCallCancelAction(){
		$helper = $this->get('wchelper');
		$request = $helper->getRequest();
		$username=$helper->getCurrentUser()->getUsername();
		$appointmentID=$request->request->get('appointmentID', '');
		$repository = $helper->getWCAppointmentRepository();
		$today = date('Y-m-d', time());
		
		//this query protects against users from injecting a different id
		//into the page and deleting appointments that aren't theirs.
		//it also protects against the same thing except appointments that
		//are their's but are not future appointments
		$query = $repository->createQueryBuilder('p')
			->where('p.id = :appID AND p.StudUsername LIKE :username AND p.EndTime >= :todayDate')
			->setParameters(array('appID' => $appointmentID, 'username' => '%'.$username.'%', 'todayDate' => $today))
			->getQuery();
			
		$data = $query->getResult();
		
		//if the data doesn't exist, don't delete it
		if(!$data){
			return new Response('Appointment failed to cancel.');
		}
		
		$data = $data[0];
    	$data->setStudUsername("");
    	$helper->flushRepository();

		return new Response('Success');
	}
}