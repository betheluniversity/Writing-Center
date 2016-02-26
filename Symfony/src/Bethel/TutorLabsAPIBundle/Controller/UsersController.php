<?php

namespace Bethel\TutorLabsAPIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bethel\TutorLabsAPIBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends Controller
{
    /**
     * @Rest\View
     */
    public function allAction() {
        $helper = $this->get('wchelper');
        $repository = $helper->getUserRepository();
        $Users = $repository->findAll();

        return array('Users' => $Users);
    }

    /**
     * @Rest\View(serializerGroups={"displayUser"})
     */
    public function byUsernameAction($username) {
        $helper = $this->get('wchelper');
        $user = $helper->getUsersWithUsernameLike($username);

        return array('Users' => $user);
    }
}