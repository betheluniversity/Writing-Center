<?php

namespace Bethel\TutorLabsBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller{

	public function adminViewAction(){
		$username = $_GET["username"];
		$helper = $this->get('wchelper');
        $request = $helper->getRequest();


        ///////////////////// WC ////////////////////
	        //Get appointments from WC
	        $wcappointments = $this->getStudentAppointments($username);

	        //This is a JSON array
	        //$wcjson = $helper->getAppointmentsAsJSON($wcappointments);
	        //$wcdata =  $helper->makeJSONResponse($wcjson);
	    /////////////////////////////////////////////

		return $this->render('BethelTutorLabsBundle:Admin:admin.html.twig',
			array(
				'username' =>	$username,
				'totalAppointments'	=>	sizeof($wcappointments), // + sizeof($mathlabappointments),
				//WC
				//'wcdata'		=>	$wcdata,
				'wcappointments'	=>	$wcappointments,
				'wcNumAppointments'	=> sizeof($wcappointments),
				//MathLab
				'mathlabNumAppointments'	=> '0',
			));
	}

	public function getStudentAppointments($username){
		$helper = $this->get('wchelper');
        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
          ->where("p.StudUsername LIKE :username AND p.CompletedTime != ''")
          ->setParameter('username', '%'.$username.'%')
          ->getQuery();

        $appointments =  $query->getResult();

		return $appointments;
	}

}