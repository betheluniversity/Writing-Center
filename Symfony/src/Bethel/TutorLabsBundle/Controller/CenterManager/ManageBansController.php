<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Bethel\TutorLabsBundle\Entity\WCStudentBans;
use DateTime;


class ManageBansController extends Controller{

	public function manageBansLoadAction(){
        /*  This loads the .html.twig file to be viewed.  */
        $helper = $this->get('wchelper');
        $repository = $helper->getWCStudentBansRepository();
        $data = $repository->findAll();
        
        $Names = $helper->getBannedUsersFirstNameLastName($data);

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_manage_bans_load.html.twig',
            array(
                'data'        => $data,
                'Names'       => $Names,
            )
        );
    }

    public function manageBansCallAction(){
        /*  This call uses ajax to unban a specified student.  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $username=$request->request->get('username', '');
        $repository = $helper->getWCStudentBansRepository();


        $data = $repository->findOneByUsername($username);

        if (!$data){
            return new Response();
        }

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->remove($data);
        $entityManager->flush();

        return new Response();
    }

    //Searches for students to ban
    public function newStudentBansAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        $username=$request->request->get('username', '');
        $firstName=$request->request->get('firstName', '');
        $lastName=$request->request->get('lastName', '');

        $unbannedUsers = $helper->getUnbannedStudentsWithUsernameFirstNameLastName($username, $firstName, $lastName);
        
        $user = array_values($unbannedUsers);

        //Gets the first/last names of students.
        $Names = $helper->getBannedUsersFirstNameLastName($user);

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_new_bans_load.html.twig',
            array(
                'data'      => $user,
                'Names'     => $Names,
            )
        );
    }

    //adds new ban
    public function newBansCallAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        $username=$request->request->get('username', '');
        $date = new DateTime();//<--automatically sets for current time

        $userRepo = $helper->getUserRepository();
        $user = $userRepo->findOneByUsername($username);

        $repository = $helper->getWCStudentBansRepository();
        $manager = $this->getDoctrine()->getManager();

        $newBan = new WCStudentBans();
        $newBan->setUsername($username);
        $newBan->setBannedDate($date);
        $newBan->setUser($user);

        $manager->persist($newBan);
        $manager->flush();

        return new Response();
    }

    //Remove all bans
    public function removeAllBansAction(){
        $helper = $this->get('wchelper');

        $repository = $helper->getWCStudentBansRepository();
        $bans = $repository->findAll();

        $manager = $this->getDoctrine()->getManager();

        foreach ($bans as $key) 
        {
            $manager->remove($key);
        }
        $manager->flush();

        return new Response();
    }

    public function manageBansViewAction(){
        /*  This view is the default view for the manage bans page.  */
        return $this->render('BethelTutorLabsBundle:CenterManager:cm_manage_bans.html.twig');
    }
}