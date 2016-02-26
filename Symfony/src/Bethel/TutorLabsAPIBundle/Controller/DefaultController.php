<?php

namespace Bethel\TutorLabsAPIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BethelTutorLabsAPIBundle:Default:index.html.twig', array('name' => $name));
    }
}
