<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class ReportsController extends Controller{

	public function reportsViewAction(){
        /*
            This is the default view for the systems view reports action. Just return the template for now.
        */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();


        $repository = $helper->getWCAppointmentRepository();
        $data = $repository->findAll();

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_reports.html.twig',
            array(
                'data'        => $data
            ));
    }

    public function reportsCallAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        // get username from POST request
        $username=$request->request->get('StudUsername', '');
        $tutorusername=$request->request->get('TutorUsername', '');
        $startdate=$request->request->get('startdate', '');
        $enddate=$request->request->get('enddate', '');
        $noShow=$request->request->get('CheckIn', '');

        if($startdate == "none")
            $startdate = null;
        if($enddate == "none")
            $enddate = null;    
        if($noShow == 4) //4 is then value when an appointment is a no show.
             $user = $helper->getAppointmentsWithNoShow($username, $tutorusername, $startdate, $enddate, $noShow);
        else
            $user = $helper->getAppointmentsWith($username, $tutorusername, $startdate, $enddate);

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_reports_call.html.twig',
            array(
                'user'      => $user
            )
        );
    }
}