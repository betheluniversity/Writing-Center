<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class PromoteController extends Controller{

	public function promoteViewAction(){
        /*
            This is the default view for the systems promote action.
            Just return the template for now.
        */
        $helper = $this->get('wchelper');
        $repository = $helper->getWCRolesRepository();
        $roles = $repository->findAll();
        //$roles = $this->container->getParameter('security.role_hierarchy.roles');
        // Create the roles select dropdown here?
        // Probably a better way but this is enough for now.
         $select = "<select style='width:90%'><option value=''>-select-</option>";
         
         // Alphabetize.
         usort($roles, function($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });

            foreach ($roles as $role) {
                if( $role->getName() == "Center Manager" || $role->getName() == "Tutor" || $role->getName() == "Student" || $role->getName() == "Observer" )
                    $select .= "<option value='".$role->getRole()."'>".$role->getName()."</option>";
            }
        $select .= "<option value='Inactive'>Inactive</option>";
        $select .= "</select>";
        return $this->render('BethelTutorLabsBundle:CenterManager:cm_promote.html.twig',
            array(
                'select'       => $select
            )
        );

    }

    public function promoteUpdateAction(){
        /*
            This is a function to support promoting a user.
            Get a username and role from POST and update that username to have the new role
        */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        // get username and role from request
        $username=$request->request->get('username', '');
        $role=$request->request->get('role', '');
        echo $username. " " . $role;
        $helper->setUserRole($username, $role);

        // Nothing to return really
        return new Response();
    }

    // public function promoteCallAction(){
    //     /*
    //         This is a function to support promoting a user in the tutorlabs software.

    //         This functions takes a POST variable called username and finds users like that username.
    //         It uses cm_promote_call.html.twig to format them nicely and returns them to the ajax function.
    //         It also includes a dropdown to select a new role.
    //     */

    //     $helper = $this->get('wchelper');
    //     $request = $helper->getRequest();

    //     // get username from POST request
    //     $username=$request->request->get('username', '');

    //     // !! This is what we would use if we knew *exactly* which username we wanted. However, we
    //     // !! want to support searching for partial usernames, so we build our our query using 'LIKE'
    //     // !! $user = $this->getDoctrine()->getRepository('BethelUserBundle:User')->findByUsername($username);

    //     $user = $helper->getUsersWithUsernameLike($username);

    //     $roles = $this->container->getParameter('security.role_hierarchy.roles');
    //     return $this->render('BethelTutorLabsBundle:CenterManager:cm_promote_call.html.twig',
    //         array(
    //             'user'        => $user,
    //         )
    //     );
    // }
}