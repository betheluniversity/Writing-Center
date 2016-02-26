<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Bethel\UserBundle\Entity\User;
use Bethel\TutorLabsBundle\Entity\WCEmailPreferences;


class CreateUserController extends Controller{

    public function createUserViewAction(){
        /*  This view is the default view for the create user page.  */
        return $this->render('BethelTutorLabsBundle:CenterManager:cm_create_user.html.twig');
    }

    public function createUserCallAction(){
        /*  This takes the username entered and locates it in banner. 
        Then it receives the array from banner of information to create
        the new user.  */

        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $manager = $this->getDoctrine()->getManager();
        $newUsername=$request->request->get('newUsername', '');

 //Find it in banner. It becomes this array.
        //if( username is not found, or is already created)
            //return new Response("cannot create new user");
        $bannerArray = array("Caleb", "Schwarze", "test123", "testing@bethel.edu", "password", "ROLE_TUTOR");

        //Create a new Email preferences
        $emailPref = new WCEmailPreferences();    
       
    //Create a new User
        $user = new User();
        $user->setLab('1');
        $user->setFirstName($bannerArray[0]);
        $user->setLastName($bannerArray[1]);
        //$user->setUsername($bannerArray[2]);
        $user->setUsername($newUsername);
        $user->setEmail($bannerArray[3]);
        $user->setPlainPassword($bannerArray[4]);
        $user->setRoles( array(
                                    "role" => $bannerArray[5],
                                ));
        $user->setEnabled(1);
        $user->setEmailPref($emailPref);

        $manager->persist($emailPref);
        $manager->persist($user);

        $helper->flushRepository();

        return new Response($user);
    }
}