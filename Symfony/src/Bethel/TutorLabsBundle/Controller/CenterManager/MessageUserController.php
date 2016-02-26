<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class MessageUserController extends Controller{

	public function messageUserViewAction(){
        /*  This view is the default view for the message users action.  */
        return $this->render('BethelTutorLabsBundle:CenterManager:cm_message_user.html.twig');
    }

    public function messageUserEmailAction(){
        /*  This method emails a specified user.  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $repository = $helper->getUserRepository();

        $username=$request->request->get('username', '');
        $subject=$request->request->get('subject', '');
        $body=$request->request->get('body', '');

        $user = $repository->findOneByUsername($username);
        $email = $user->getEmail();

        $helper->sendMessage($email, $subject, $body);

        return new Response();
    }

    public function messageUserCallAction(){
        /*  This call uses ajax to search for students to display on the page.  */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username from POST request
        $username=$request->request->get('username', '');
        $user = $helper->getUsersWithUsernameLike($username);

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_message_user_call.html.twig',
            array(
                'user'        => $user
            )
        );
    }

}