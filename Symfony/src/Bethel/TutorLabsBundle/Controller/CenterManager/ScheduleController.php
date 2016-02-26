<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Bethel\TutorLabsBundle\Entity\WCSchedule;
use DatePeriod;
use DateTime;
use DateInterval;


class ScheduleController extends Controller{

	public function scheduleLoadAction(){
        /*  This loads the .html.twig page to be viewed. */
        $helper = $this->get('wchelper');
        $repository = $helper->getWCScheduleRepository();

        $data = array();
        for($i = 1; $i < 8; $i++) {
             $theDay = $helper->getSlotsForDay($i); //Get the slots for each day.

             usort($theDay, function($a, $b){  //A simple compareTo function for time sorting.
                $t1 = strtotime($a->getTimeStart()->format('g:i a'));
                $t2 = strtotime($b->getTimeStart()->format('g:i a'));
                return ($t1 - $t2);
            });
            $data = array_merge($data, $theDay);
        }
        return $this->render('BethelTutorLabsBundle:CenterManager:cm_schedule_load.html.twig',
            array(
                'data'        => $data,
            ));
    }

    public function scheduleViewAction(){
        /*  This is the default view for the Lab Schedule page.  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        $repository = $helper->getWCScheduleRepository();
        $data = $repository->findAll();
        return $this->render('BethelTutorLabsBundle:CenterManager:cm_schedule_view.html.twig',
            array(
                'data'        => $data
            ));
    }

    public function scheduleCallToggleAction(){
        /*  This call uses ajax to change whether a schedule is 'active' or not.  */
          $helper = $this->get('wchelper');
          $request = $helper->getRequest();
          $timeID=intval($request->request->get('timeID', ''));
          $isActive=$request->request->get('isActive', '');

          //A simple reverse of "Yes" and "No".
          if($isActive == "Yes")
              $isActive = "No";
          else
              $isActive = "Yes";

          $repository = $helper->getWCScheduleRepository();
          $data = $repository->findById($timeID);
          $data = $data[0]; //Index should be 0 because there is only one element that has ID, timeID.
          $data->setIsActive($isActive);
          $helper->flushRepository();

          $response = new JsonResponse();
          $response->setData(array(
              // use the object to get the new value just to be safe. It will be the same though
              'isActive' => $data->getIsActive()
          ));
          return $response;
    }

    public function scheduleCallAddAction(){
        /*
            This call uses ajax to add a new specified time slot to be added
            to the schedule.
        */
          $helper = $this->get('wchelper');
          $request = $helper->getRequest();
          $repository = $helper->getWCScheduleRepository();
          $manager = $this->getDoctrine()->getManager();

        //Get values
        
          //Formatting the dates correctly
          $timeStart=$request->request->get('start', '');
          $start = date_create($timeStart);

          $timeEnd=$request->request->get('end', '');
          $end = date_create($timeEnd);

          $isActive=$request->request->get('isActive', 'No');

          //Create and set the 'time slot'
           $newSlot = new WCSchedule();
           $newSlot->setTimeStart($start);
           $newSlot->setTimeEnd($end);
           $newSlot->setIsActive($isActive);

           $manager->persist($newSlot);

           $helper->flushRepository();

          return new Response($newSlot->getID());
    }

    public function scheduleCallUpdateAction(){
          $helper = $this->get('wchelper');
          $request = $helper->getRequest();
          $repository = $helper->getWCScheduleRepository();
          $manager = $this->getDoctrine()->getManager();

          //Get values
          $id=intval($request->request->get('row', ''));
          $dayOfWeek=$request->request->get('dow', '');

          //Formatting the dates correctly
          $timeStart=$request->request->get('start', '');
          $start = date_create($timeStart);
          $timeEnd=$request->request->get('end', '');
          $end = date_create($timeEnd);
          
          $isActive=$request->request->get('active', '');
          $data = $repository->findOneById($id);
          if($data == null){
            return new Response($id);
          }
          $data->setTimeStart($start);
          $data->setTimeEnd($end);
          $data->setIsActive($isActive);

           $helper->flushRepository();

          return new Response();
    }

    public function scheduleCallRemoveAction(){
        /*  This call uses ajax to remove a specified time slot from the schedule.  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $repository = $helper->getWCScheduleRepository();
        $id=$request->request->get('id', '');

        $data = $repository->findOneById($id);
        if (!$data){
            return new Response();
        }

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->remove($data);
        $entityManager->flush();

        return new Response();
    }
}