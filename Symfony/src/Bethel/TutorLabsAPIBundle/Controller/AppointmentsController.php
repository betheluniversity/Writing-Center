<?php

namespace Bethel\TutorLabsAPIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bethel\TutorLabsAPIBundle\Entity\WCAppointmentData;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AppointmentsController extends Controller
{
    /**
     * @Rest\View
     */
    public function allAction() {
        $helper = $this->get('wchelper');
        $repository = $helper->getWCAppointmentRepository();
        $appointments = $repository->findAll();

        return array('appointments' => $appointments);
    }

    /**
     * @Rest\View(serializerGroups={"displayAppointments"})
     */
    public function byStudentAction($name) { //username
        $helper = $this->get('wchelper');
        $appointments = $helper->getAppointmentsWithStudent($name); //username

        if (sizeof($appointments) == 0) {
            throw new NotFoundHttpException('No appointments not found for the student: '.$name);
        }

        return array('appointments' => $appointments);
    }

    /**
     * @Rest\View(serializerGroups={"displayAppointments"})
     */
    public function byTutorAction($name) { //username
        $helper = $this->get('wchelper');
        $appointments = $helper->getAppointmentsWithTutor($name); //username

        if (sizeof($appointments) == 0) {
            throw new NotFoundHttpException('No appointments not found for the tutor: '.$name);
        }

        return array('appointments' => $appointments);
    }

    // /**
    //  * @Rest\View(serializerGroups={"displayAppointments"})
    //  */
    // public function inDateRangeAction($start, $end) { //start and end dates
    //     $helper = $this->get('wchelper');
    //     $appointments = $helper->getAppointmentsInDateRange($start, $end); //username

    //     if (sizeof($appointments) == 0) {
    //         throw new NotFoundHttpException('No appointments not found in the date range: '.$start.' - '.$end);
    //     }

    //     return array('appointments' => $appointments);
    // }
}