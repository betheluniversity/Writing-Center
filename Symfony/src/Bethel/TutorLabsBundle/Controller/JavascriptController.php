<?php
// src/Blogger/BlogBundle/Controller/PageController.php

namespace Bethel\TutorLabsBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JavascriptController extends Controller
{
    var $encoder;
    var $normalizer;
    var $serializer;

    function __construct(){
        $this->encoder = array(new JsonEncoder());
        $this->normalizer = array(new GetSetMethodNormalizer());
        $this->serializer = new Serializer($this->normalizer, $this->encoder);
    }

    public function getUsernamesLikeAction(){

        /** @var $helper \Bethel\TutorLabsBundle\Helper\WCHelper */
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();
        $username=$request->request->get('username', '');

        //Stopped using this to stop the user from demoting/promoting themselves.
        $usernames = $helper->getUsersWithUsernameLike($username);

        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($usernames, 'json');

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function getUsernamesLikeFirstLastNamesAction(){
        $helper = $this->get('wchelper');
        $request = $helper->getRequest();

        $username=$request->request->get('username', '');
        $first=$request->request->get('first', '');
        $last=$request->request->get('last', '');
        $usernames = $helper->getUsersWithUsernameFirstNameLastName($username, $first, $last); //usernames is an array of objects

        //Code to prevent a user from changing their own role.
         $currentUser = $helper->getCurrentUser()->getUsername();

         $indexToRemove = 0; 
         foreach($usernames as $username){
            if($username->getUsername() == $currentUser){
                unset($usernames[$indexToRemove]); //remove the current user's object, which is $username.
                break;
            }
            $indexToRemove++;
         }
         $usernames = array_values($usernames);

        $serializer = $this->get('jms_serializer');
        $jsonContent = $serializer->serialize($usernames, 'json');

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function getLabScheduleAction(){

        $helper = $this->get('wchelper');
        
        $repository = $helper->getWCScheduleRepository();
        $data = $repository->findAll();

        $jsonContent = $this->serializer->serialize($data, 'json');

        // our JS template doesn't seem to render time nicely. I also can't add a field to the original array,
        // because it is a PHP object. So convert to json, decode to normal array, and then add the field.
        $data = json_decode($jsonContent);
        $days = array("1"=>"Monday","2"=>"Tuesday","3"=>"Wednesday","4"=>"Thursday","5"=>"Friday","6"=>"Saturday","7"=>"Sunday");
        for($i = 0; $i < count($data); ++$i) {
            $data[$i]->readableStartTime = gmdate("g:i a",intval($data[$i]->timeStart->timestamp) + intval($data[$i]->timeStart->offset));
            $data[$i]->readableEndTime = gmdate("g:i a",intval($data[$i]->timeEnd->timestamp) + intval($data[$i]->timeEnd->offset));


        }
        $jsonContent = json_encode($data);
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
