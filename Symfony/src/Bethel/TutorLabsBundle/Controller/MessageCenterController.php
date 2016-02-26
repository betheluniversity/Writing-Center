<?php

namespace Bethel\TutorLabsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Bethel\TutorLabsBundle\Form\WCEmailPreferencesType;
use Bethel\TutorLabsBundle\Entity\WCEmailPreferences;

class MessageCenterController extends Controller{

    public function messageViewAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $repository = $helper->getUserRepository();
        // get username and role from POST request
        $username = $helper->getCurrentUser()->getUsername();
        //$username = $request->request->get('username', '');
        $role = $request->request->get('role', '');

        /* Standard view function. */
        if($role == "ROLE_ADMIN" || "ROLE_GLOBAL_ADMIN" || "ROLE_CENTER_MANAGER" || "ROLE_TUTOR"){
            return $this->render('BethelTutorLabsBundle::wc_message_center.html.twig');
         }
         else
           return;
    }

     public function messagePreferencesViewAction(Request $request){
        /* Standard view function. */
        $helper = $this->get('wchelper');
        //$request = $helper->getRequest();
        // get username from POST request
        $user=$helper->getCurrentUser();
        $username = $user->getUsername();
        if($user->getEmailPref())
            $emailPref = $user->getEmailPref();
        else
            $emailPref = new WCEmailPreferences();

        $form = $this->createForm(new WCEmailPreferencesType($this->get('security.context')
                                           ->isGranted('ROLE_TUTOR')), $emailPref);  
        $form->handleRequest($request);
        if($form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($emailPref);
            $em->flush();

            //need to set the users $email_pref id to the id of the new WCEmailPreferences.
            $repository = $helper->getUserRepository();
            $user = $repository->findOneByUsername($username);
            $user->setEmailPref($emailPref);
            $em->flush();

            //return some kind of success message here?
            return $this->render('BethelTutorLabsBundle::wc_message_center.html.twig',
            array('user'        => $user,
                  'form'        => $form->createView(),
                )
            );
        }
        //If the form is submitted and not vaild.
        else if($form->isSubmitted())
        {
            //return a error message saying it didn't work.
        }


      return $this->render('BethelTutorLabsBundle::wc_message_center_preferences.html.twig',
      	array('user'		=> $user,
              'form'        => $form->createView(),
      		)
      	);
     }

    public function messageUsersViewAction(){
        /* Standard view function. */
      $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $username = $helper->getCurrentUser()->getUsername();
        //$username = $request->request->get('username', '');
        $user = $username;
      return $this->render('BethelTutorLabsBundle::wc_message_center_users.html.twig',
      	array('user'		=> $user,
      		)
      	);
    }


    public function messageStudentViewAction(){
        /* Standard view function. */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $username = $helper->getCurrentUser()->getUsername();
      return $this->render('BethelTutorLabsBundle::wc_message_center_students.html.twig',
        array('user'        => $username,
            )
        );
    }

    public function messageTutorViewAction(){
        /* Standard view function. */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $username = $helper->getCurrentUser()->getUsername();
      return $this->render('BethelTutorLabsBundle::wc_message_center_tutors.html.twig',
        array('user'        => $username,
            )
        );
    }

 public function messageStudentEmailAction(){
        /*  This method emails a specified user.  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $repository = $helper->getUserRepository();


        $subject=$request->request->get('subject', '');
        $body=$request->request->get('body', '');
        $student = $helper->getAllStudents();
        $tutor = $helper->getAllTutors();

    //Emailing students also includes all tutors.
        foreach($student as $temp1){
          $email = $temp1->getEmail();
          $helper->sendMessage($email, $subject, $body);
        }
         foreach($tutor as $temp1){
          $email = $temp1->getEmail();
          $helper->sendMessage($email, $subject, $body);
         }
        return new Response();

    }

 public function messageTutorEmailAction(){
        /*  This method emails a specified user.  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $repository = $helper->getUserRepository();

        $subject=$request->request->get('subject', '');
        $body=$request->request->get('body', '');
        $tutor = $helper->getAllTutors();

        foreach($tutor as $temp1){
          $email = $temp1->getEmail();
          $helper->sendMessage($email, $subject, $body);
        }
        

        return new Response();

    }
    public function messageUserEmailAction(){
        /*  This method emails a specified user.  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $repository = $helper->getUserRepository();

        $username = $helper->getCurrentUser()->getUsername();
        $subject=$request->request->get('subject', '');
        $body=$request->request->get('body', '');
        $user = $repository->findOneByUsername($username);

        $email = $user->getEmail();
        
        $helper->sendMessage($email, $subject, $body);
        
        return new Response();

    }

    public function messageUserCallAction(){
        /*  This call uses ajax to search by username and role to email users  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $repository = $helper->getUserRepository();
        // get username and role from POST request
        //$username = $helper->getCurrentUser()->getUsername();
        $username = $request->request->get('username', '');
        $firstName = $request->request->get('firstName', '');
        $lastName = $request->request->get('lastName', '');
        $role = $request->request->get('role', '');

        //Global Admin, Admin, and CM can send emails to anyone.
        if($role == "ROLE_ADMIN" || "ROLE_GLOBAL_ADMIN" || "ROLE_CENTER_MANAGER"){
          $query = $repository->createQueryBuilder('p')
            ->where('p.username LIKE :username AND p.firstName LIKE :firstName AND p.lastName LIKE :lastName')
            ->setParameters(array("username"=> '%'.$username.'%', "firstName"=> '%'.$firstName.'%', "lastName"=> '%'.$lastName.'%'))
            ->getQuery();
        }   
        //Tutors can only send to tutors and CMs.
        if($role == "ROLE_TUTOR"){
          $query = $repository->createQueryBuilder('p')
            ->where('p.username LIKE :username OR p.roles LIKE :allowCM OR p.roles LIKE :allowTutor AND p.firstName LIKE :firstName AND p.lastName LIKE :lastName')
            ->setParameters(array('username' => '%'.$username.'%', 'allowCM' => '%ROLE_CENTER_MANAGER%', 'allowTutor' => '%ROLE_TUTOR%', "firstName"=> '%'.$firstName.'%', "lastName"=> '%'.$lastName.'%'))
            ->getQuery();
        }   

        $user = $query->getResult();//will need to render something else here
        return $this->render('BethelTutorLabsBundle::wc_message_center_users_load.html.twig',
            array(
                'user'        => $user,
            )
        );
    }
}