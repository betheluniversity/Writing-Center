<?php

namespace Bethel\TutorLabsAPIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bethel\TutorLabsAPIBundle\Entity\WCSystemSettings;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingsController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"displaySettings"})
     */
    public function allAction() {
        $helper = $this->get('wchelper');
        $repository = $helper->getWCSystemSettingsRepository();
        $settings = $repository->findAll();

        return array('settings' => $settings);
    }
}