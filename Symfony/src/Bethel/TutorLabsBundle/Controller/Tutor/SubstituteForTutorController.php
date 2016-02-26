<?php

namespace Bethel\TutorLabsBundle\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SubstituteForTutorController extends Controller{

    // base page
    public function substituteForTutorViewAction() {
      return $this->render('BethelTutorLabsBundle:Tutor:tutor_substitute_for_tutor.html.twig');
    }

    // returns the table of tutors who need subs
    public function substituteForTutorLoadAction() {
        $helper = $this->get('wchelper');
        $json = $helper->getAppointmentsAsJSON($helper->getSubstituteData());
    	return $helper->makeJSONResponse($json);
    }

    // updates the database with the new sub's info and displays the new table
    public function substituteForTutorCallAction() {

        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        $appointmentID=$request->request->get('appointmentID', '');
        $username=$helper->getCurrentUser()->getUsername();
        
        $repository = $helper->getWCAppointmentRepository();

        $query = $repository->createQueryBuilder('p')
          ->where("p.id = :appointmentID")
          ->setParameter('appointmentID', $appointmentID)
          ->getQuery();

        $data =  $query->getResult();
        if (sizeof($data) == 0) {
            return new Response("no results were found for appointmentID='".$appointmentID."' and username='".$username."'");
        }
        $data = $data[0];
        $oldTutorUsername = $data->getTutorUsername();
        $data->setTutorUsername($username);
        $data->setRequestSub("No");

        $helper->flushRepository();

        $oldTutor = $helper->getStudentsWithUsernameLike($oldTutorUsername);
        $email = $oldTutor->getEmail();
        $subject = "TutorLabs - Shift Successfully Subbed";
        $body = $helper->getFirstLastNameByUsername( $data->getTutorUsername() )." has subbed for one of your shifts. \n".
                "Date: ".$data->getStartTime()->format("m/d/Y")."   \n".
                "Time: ".$data->getStartTime()->format("g:ia")." - ".$data->getEndTime()->format("g:ia")."   \n";
        $helper->sendMessage($email, $subject, $body);

        return new Response("Success");
    }
 }