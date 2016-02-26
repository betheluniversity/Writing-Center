<?php

namespace Bethel\TutorLabsBundle\Controller\Tutor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class HoursWorkedController extends Controller{

    public function hoursWorkedViewAction(){
        /* Standard view function. */
      return $this->render('BethelTutorLabsBundle:Tutor:tutor_hours_worked.html.twig');
    }

    public function hoursWorkedLoadAction(){
        /* This takes in a range of dates and displays the shifts the user has worked.*/
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $username=$request->request->get('username', '');
        $startdate=$request->request->get('startdate', '');
        $enddate=$request->request->get('enddate', '');
        $user = $helper->getUsersWithUsernameLike($username);
        $repository = $helper->getWCAppointmentRepository();

        $now = date('Y-m-d H:i:s a', time());
        $query = $repository->createQueryBuilder('p')
        ->where('p.TutorUsername = :username AND p.CompletedTime <= :now AND p.CompletedTime >= :startdate AND p.CompletedTime <= :enddate')
        ->setParameters(array('username' => $username, 'now' => $now, 'startdate' => $startdate, 'enddate' => $enddate))
        ->orderBy('p.StartTime', 'DESC')
        ->getQuery();

        $data =  $query->getResult();

        // Temporarily change the array so that each has a first/last name.
        $helper->quickFixStudentTutorUsernames( $data);

        $hoursWorkedArray = $this->getHoursWorked($data);
        $totalHours = 0;
        for($i = 0; $i < sizeof($hoursWorkedArray);$i++)
            $totalHours = $totalHours + $hoursWorkedArray[$i];
        return $this->render('BethelTutorLabsBundle:Tutor:tutor_hours_worked_load.html.twig',
            array(
                'data'                  =>    $data,
                'hoursWorkedArray'      =>    $hoursWorkedArray,
                'totalHours'            =>    $totalHours,
            )
        );
    }


    public function getHoursWorked($completedAppointments){
        /* This function takes in a seriest of appointments and returns an array of the hours worked. (completed time) - (start time)*/
        //This function approximates the time worked based on the actual start time.

        //Make an array of hours worked.
        $hoursWorked = array();
        for( $i = 0; $i < sizeof($completedAppointments); $i++)
        {
            ///////////////////////////// Start Time////////////////////////////
            if($completedAppointments[$i]->getActualStartTime() == null)
                $StartTime = date_format($completedAppointments[$i]->getStartTime(), 'H i');
            else
                $StartTime = date_format($completedAppointments[$i]->getActualStartTime(), 'H i');
            $Start = split(" ", $StartTime);
            $StartHour = $Start[0];
            $StartMin = $Start[1];

                        //test//
            //echo "<script>console.log($StartHour + ':' + $StartMin + ' ' + $CompletedHour + ':' + $CompletedMin)</script>";
                        //endtest//

            //Round to the next 15 minutes.
            if(0 < $StartMin && $StartMin < 15)
              $StartMin = 15;
            else if(15 < $StartMin && $StartMin < 30)
              $StartMin = 30;
            else if(30 < $StartMin && $StartMin < 45)
              $StartMin = 45;
            else if(45 < $StartMin && $StartMin < 60){
              $StartMin = 0;
              if($StartHour < 24)
                  $StartHour = $StartHour + 1;
              //else
                    //we have a problem if a shift starts between 11:45 and 11:59 at night.
            }

            ///////////////////////////// Completed Time////////////////////////////
            $CompletedTime = date_format($completedAppointments[$i]->getCompletedTime(), 'H i');
            $Completed = split(" ", $CompletedTime);
            $CompletedHour = $Completed[0];
            $CompletedMin = $Completed[1];
            $StartTime;


            //Round to the next 15 minutes.
            if(0 < $CompletedMin && $CompletedMin < 15)
              $CompletedMin = 15;
            else if(15 < $CompletedMin && $CompletedMin < 30)
              $CompletedMin = 30;
            else if(30 < $CompletedMin && $CompletedMin < 45)
              $CompletedMin = 45;
            else if(45 < $CompletedMin && $CompletedMin < 60){
              $CompletedMin = 0;
              if($CompletedHour < 24)
                  $CompletedHour = $CompletedHour + 1;
                //else
                    //we have a problem if a shift ever ends between 11:45 and 11:59 at night.
            }




            $TotalHours = $CompletedHour - $StartHour + ($CompletedMin - $StartMin)/60;
            array_push($hoursWorked, $TotalHours);
        }
        return $hoursWorked;
    }
}