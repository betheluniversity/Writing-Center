<?php

namespace Bethel\TutorLabsBundle\Controller\CenterManager;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class BannerController extends Controller{

    public function bannerPreviewCallAction(){
        
        $helper = $this->get('wchelper');
        $wsapi = $this->get('wsapi');

        $request = $helper->getRequest();
        // get username from POST request
        $username=$request->request->get('username', 'mwe43644');

        $roles = $wsapi->getRoles($username);
        echo "<i> These are examples of how to scan roles and courses in PHP </i>";
        echo "<pre>";
        // If i wanted to look through all roles the API sent back:
        foreach ($roles['Roles'] as $index => $role) {
            // look for the role you are interested in.
            //list($label, $role) = explode(":", $role);
            echo $role . "\n";
        }
        echo "</pre>";
        
        $courses = $wsapi->getCourses($username);
        echo "<pre>";
        echo "<ul>";
        foreach ($courses['Courses'] as $course => $info) {
            // look for the role you are interested in.
            // $course is Subject Code + Course number
            // e.g. COS101 or MAT204. $info is an array
            // containing course title and instructor.
            // 
            //list($course_label, $course) = explode(":", $course); 
            //list($title_label, $title) = explode(":", $info['title']); 
            //list($instructor_label, $instructor) = explode(":", $info['instructor']); 

            echo $course;
            echo "<li>$course</li>";
            echo '<ul>';
            echo "<li>" . $info['title'] . "</li>";
            echo "<li>" . $info['instructorUsername'] . "</li>";
            echo '</ul>';

        }
        echo "</ul>";
        echo "</pre>";

        $students = $wsapi->getStudents($username);
        echo "<pre>";
        echo "<ul>";
        foreach ($students as $index => $info){
            echo "<li>" . $info['Student-Name'] . "</li>";
            echo "<ul>";
            echo "<li>" . $info['Course'] . "</li>";
            echo "<li>" . $info['Semester'] . "</li>";
            echo "<li>" . $info['username'] . "</li>";
            echo "</ul>";
        }
        echo "</ul></pre>";
        

        return $this->render('BethelTutorLabsBundle:CenterManager:cm_banner_preview_call.html.twig',
            array('roles' => $roles, 'courses' => $courses['Courses'], 'students' => $students)
        );
    }

    public function bannerPreviewViewAction(){
        return $this->render('BethelTutorLabsBundle:CenterManager:cm_banner_preview.html.twig');

    }
}