<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class SystemSettingsController extends Controller{

	public function systemSettingsViewAction(){
        /*  This view is the default view for the System Settings page.  */

        $helper = $this->get('wchelper');
        $repository = $helper->getWCSystemSettingsRepository();

        $settings = $repository->findAll();

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_system_settings.html.twig',
            array(
                'apptLimit'        => $settings[0]->getApptLimit(),
                'timeLimit'        => $settings[0]->getTimeLimit(),
                'banLimit'        => $settings[0]->getBanLimit(),
                'qualtricsLink'    => $settings[0]->getQualtricsLink()
            )
        );
    }

    public function systemSettingsUpdateAction(){
        /*   This update changes the current settings to the new specified settings.  */

        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        $repository = $helper->getWCSystemSettingsRepository();
        $settings = $repository->findAll();
        $settings = $settings[0]; // Only 1 row in table, so grab the first row
        $apptLimit=$request->request->get('apptLimit');
        $timeLimit=$request->request->get('timeLimit');
        $banLimit=$request->request->get('banLimit');
        $qualtricsLink=$request->request->get('qualtricsLink');

        $settings->setApptLimit($apptLimit);
        $settings->setTimeLimit($timeLimit);
        $settings->setBanLimit($banLimit);
        $settings->setQualtricsLink($qualtricsLink);

        $helper->flushRepository();

        $response = new JsonResponse();
        $response->setData(array(
            // use the object to get the new value just to be safe. It will be the same though
            'apptLimit' => $settings->getApptLimit(),
            'timeLimit' => $settings->getTimeLimit(),
            'banLimit' => $settings->getBanLimit(),
            'qualtricsLink' => $settings->getQualtricsLink(),
        ));
        return $response;

    }
}