<?php

namespace Bethel\TutorLabsBundle\Controller\Student;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class AppointmentHistoryController extends Controller{

    public function appointmentHistoryViewAction(){
        return $this->render('BethelTutorLabsBundle:Student:student_history.html.twig');
    }
	public function appointmentHistoryLoadAction(){
        /*
            This is the default view for the systems view reports action. Just return the template for now.
        */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $username=$helper->getCurrentUser()->getUsername();
        $repository = $helper->getWCAppointmentRepository();
        $now = date_create(date('Y-m-d H:i:s', time()));
        $query = $repository->createQueryBuilder('p')
          ->where("p.StudUsername LIKE :studentUsername", "p.CompletedTime < :now")
          ->setParameters(array('studentUsername' => '%'.$username.'%', 'now' => $now))
          ->getQuery();

        $data = $query->getResult();

        $json = $helper->getAppointmentsAsJSON($data);
        return $helper->makeJSONResponse($json);
    }
}