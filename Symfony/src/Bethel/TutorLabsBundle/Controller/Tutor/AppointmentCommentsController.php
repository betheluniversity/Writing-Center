<?php

namespace Bethel\TutorLabsBundle\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AppointmentCommentsController extends Controller{

    public function appointmentCommentsViewAction(){
        return $this->render('BethelTutorLabsBundle:Tutor:tutor_appointment_comments.html.twig');
    }

    public function appointmentCommentsLoadAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        
        $firstName = $request->request->get('firstName', '');
        $lastName = $request->request->get('lastName', '');
        $appointmentDate = $request->request->get('appointmentDate', '');
        $tutorUsername = $request->request->get('tutorUsername', '');

        // find possible usernames for the given first and last name
        $userRepository = $helper->getUserRepository();
        $userQuery = $userRepository->createQueryBuilder('p')
            ->where('p.firstName LIKE :firstName', 
                    'p.lastName LIKE :lastName')
            ->setParameters(array('firstName' => '%' . $firstName . '%', 
                                  'lastName' => '%' . $lastName . '%'))
            ->getQuery();

        // if we didn't find any, tell the user
        $usersdata =  $userQuery->getResult();
        if (sizeof($usersdata) == 0) {
            return new Response("No users were found for ".$firstName." ".$lastName." on ".$appointmentDate.".");
        }
        
        // create an array that has user names as keys and first / last names as values
        $usernameToActualNameMapping = array();
        foreach ($usersdata as $user) {
            $usernameToActualNameMapping[$user->getUsername()] = array('first' => $user->getFirstName(), 
                                                                       'last' => $user->getLastName());
        }

        // create a list of all usernames we found
        $usernameList = array_keys($usernameToActualNameMapping);


        // Find all of the appointments for the selected day where the current 
        //     tutor met with one of the users found above
        $appointmentsRepository = $helper->getWCAppointmentRepository();
        
        $qb = $appointmentsRepository->createQueryBuilder('p');
        $qb ->add('where', $qb->expr()->in('p.StudUsername', ':usernameList'))
            ->andWhere('p.TutorUsername LIKE :tutorUsername AND p.Comment LIKE :comments OR p.Suggestion LIKE :suggestion OR p.Assignment LIKE :assignment')
            ->setParameters(array('usernameList' => $usernameList, 'tutorUsername' => '%'.$tutorUsername.'%', 'comments' => "", 'suggestion' => "", 'assignment' => "")) 
            ->add('orderBy', 'p.StartTime DESC');

        // Filter by date if the user picked one
        if ($appointmentDate) {
            $qb ->andWhere('p.StartTime >= :start')
                ->andWhere('p.StartTime <= :end')
                ->setParameter('start', $appointmentDate." 00:00:00")
                ->setParameter('end', $appointmentDate." 23:59:59");
        }

        $appointmentData =  $qb->getQuery()->getResult();

        // Tell the user if we didn't find anything
        if (sizeof($appointmentData) == 0) {
            return new Response("No appointments matching your search without reports were found.");
        }

        // Create an array that contains pairs of appointments and the first/last names of the student in the appointment 
        $appointmentDataWithNames = array();
        foreach ($appointmentData as $appointment) {
            $currentUserName = $appointment->getStudUsername();
            $appointmentDataWithNames[] = array($appointment, $usernameToActualNameMapping[$currentUserName]);
        }

        return $this->render('BethelTutorLabsBundle:Tutor:tutor_appointment_comments_load.html.twig',
            array(
                'data' => $appointmentDataWithNames
            )
        );
    }

    public function appointmentCommentsCallAction(){

        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        
        $appointmentID = $request->request->get('appointmentID', '');
        $assignmentText = $request->request->get('assignmentText', '');
        $commentText = $request->request->get('commentText', '');
        $suggestionText = $request->request->get('suggestionText', '');

        $appointmentsRepository = $helper->getWCAppointmentRepository();
        $appointmentsQuery = $appointmentsRepository->createQueryBuilder('p')
            ->where('p.id = :appointmentID')
            ->setParameter('appointmentID', $appointmentID)
            ->getQuery();

        $appointments =  $appointmentsQuery->getResult();

        if (sizeof($appointments) == 0) {
            return new Response("No appointment found to update. Please refresh the page and try again.");
        }

        $appointment = $appointments[0];

        $appointment->setAssignment($assignmentText);
        $appointment->setComment($commentText);
        $appointment->setSuggestion($suggestionText);

        $helper->flushRepository();

        return new Response("Successfuly updated appointment.");
    }
}