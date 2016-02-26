<?php

namespace Bethel\TutorLabsAPIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bethel\TutorLabsAPIBundle\Entity\WCStudentBans;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BansController extends Controller
{
    /**
     * @Rest\View
     */
    public function allAction() {
        $helper = $this->get('wchelper');
        $repository = $helper->getWCStudentBansRepository();
        $students = $repository->findAll();

        return array('students' => $students);
    }

    /**
     * @Rest\View(serializerGroups={"displayBans"})
     */
    public function byIDAction($id) { //username
        $helper = $this->get('wchelper');
        $repository = $helper->getWCStudentBansRepository();
        $banned = $repository->findOneById($id);

        // if (!$banned == 0) {
        //     throw new NotFoundHttpException('No appointments not found for the student: '.$name);
        // }

        return array('banned' => $banned);
    }
}