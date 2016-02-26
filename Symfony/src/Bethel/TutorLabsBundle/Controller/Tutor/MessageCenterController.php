<?php

namespace Bethel\TutorLabsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class MessageCenterController extends Controller{

    public function messageViewAction(){
        /* Standard view function. */
      return $this->render('BethelTutorLabsBundle:WritingCenter:wc_message_center.html.twig');
    }
}