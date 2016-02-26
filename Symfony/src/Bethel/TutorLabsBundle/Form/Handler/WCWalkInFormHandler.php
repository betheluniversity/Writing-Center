<?php

namespace Bethel\TutorLabsBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Bethel\TutorLabsBundle\Entity\WCAppointmentData;

class WCWalkInFormHandler {
    protected $em;
    protected $requestStack;
    protected $session;

    public function __construct(EntityManager $em, RequestStack $requestStack, Session $session) {
        $this->em = $em;
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $session;
    }

    public function process(Form $form) {
        if('POST' !== $this->request->getMethod()) {
            return false;
        }
        
        // var_dump($form->isValid());
        // die;
        if($form->isValid()) {
            return $this->processValidForm($form);
        }

        return false;
    }

    /**
     * Processes the valid form
     *
     * @param Form $form
     * @return WCAppointmentData
     */
    public function processValidForm(Form $form) {
        $form->getData();
        $currentTime = date_create(date('Y-m-d H:i:s', time()));
        $newAppt->setEndTime($currentTime);
        $newAppt->setStudentSignOut($currentTime);
        $newAppt->setCompletedTime($currentTime);
        $newAppt->setCheckIn(5);

        $first = $form->get('first')->getData();
        $last = $form->get('last')->getData();
        $email = $form->get('email')->getData();
        $ferpaEmail = $form->get('ferpaAgreement')->getData();
        $CourseCode = $newAppt->getCourseCode();

        // $profEmail = $helper->getProfEmail($newAppt->getStudUsername(), $CourseCode);
        // if($profEmail == "Failed"){
        //     return $this->render('BethelTutorLabsBundle:Tutor:tutor_walk_in_coursecodefailed.html.twig');
        // }

        // if($ferpaEmail == '1') //***CHANGE TO TUTEE***//
        //     $helper->emailTutorAndInstructor($profEmail, $helper->getCurrentUser()->getEmail(), $newAppt); //Need to include TutorEmail and ProfEmail.

        // $this->createUser($newAppt, $first, $last, $email); //Creates the new user, otherwise do nothing.
        // $newAppt->setProfEmail($profEmail);
        $this->em->persist($newAppt);
        $this->em->flush();

        return $newAppt;
    }
}