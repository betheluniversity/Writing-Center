<?php

namespace Bethel\TutorLabsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('BethelTutorLabsBundle:Default:index.html.twig');
    }
}
