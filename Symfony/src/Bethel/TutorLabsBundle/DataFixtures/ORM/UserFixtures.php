<?php
// src/Blogger/BlogBundle/DataFixtures/ORM/BlogFixtures.php

namespace Bethel\TutorLabsBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bethel\UserBundle\Entity\User;
use Bethel\UserBundle\Entity\Role;

class UserFixtures implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $em;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = NULL)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $globaladminRole = $this->em->getRepository('BethelUserBundle:Role')->findOneBy(array(
        'role' => 'ROLE_GLOBAL_ADMIN'
        ));

        $adminRole = $this->em->getRepository('BethelUserBundle:Role')->findOneBy(array(
        'role' => 'ROLE_ADMIN'
        ));

        $cmRole = $this->em->getRepository('BethelUserBundle:Role')->findOneBy(array(
        'role' => 'ROLE_CENTER_MANAGER'
        ));

        $observerRole = $this->em->getRepository('BethelUserBundle:Role')->findOneBy(array(
        'role' => 'ROLE_OBSERVER'
        ));

        $tutorRole = $this->em->getRepository('BethelUserBundle:Role')->findOneBy(array(
        'role' => 'ROLE_TUTOR'
        ));

        $studentRole = $this->em->getRepository('BethelUserBundle:Role')->findOneBy(array(
        'role' => 'ROLE_STUDENT'
        ));

        $apiuserRole = $this->em->getRepository('BethelUserBundle:Role')->findOneBy(array(
        'role' => 'ROLE_API_USER'
        ));

        $globaladmin = new User();
        $globaladmin->setFirstName('Bob');
        $globaladmin->setLastName('Johnson');
        $globaladmin->setUsername('globaladmin');
        $globaladmin->setEmail('globaladmin@bethel.edu');
        $globaladmin->setPassword(NULL);
        $globaladmin->addRole($globaladminRole);
        $globaladmin->setEnabled(1);
        $manager->persist($globaladmin);

        $admin = new User();
        $admin->setFirstName('Jim');
        $admin->setLastName('Anderson');
        $admin->setUsername('admin');
        $admin->setEmail('admin@bethel.edu');
        $admin->setPassword(NULL);
        $admin->addRole($adminRole);
        $admin->setEnabled(1);
        $manager->persist($admin);

        $centermanager = new User();
        $centermanager->setFirstName('April');
        $centermanager->setLastName('Schmidt');
        $centermanager->setUsername('centermanager');
        $centermanager->setEmail('ces55739@bethel.edu');
        $centermanager->setPassword(NULL);
        $centermanager->addRole($cmRole);
        $centermanager->setEnabled(1);
        $manager->persist($centermanager);

        $centermanagerEJ = new User();
        $centermanagerEJ->setFirstName('Eric');
        $centermanagerEJ->setLastName('Jameson');
        $centermanagerEJ->setUsername('ejc84332');
        $centermanagerEJ->setEmail('ces55739@bethel.edu');
        $centermanagerEJ->setPassword(NULL);
        $centermanagerEJ->addRole($cmRole);
        $centermanagerEJ->setEnabled(1);
        $manager->persist($centermanagerEJ);

        $observer = new User();
        $observer->setFirstName('obsFirst');
        $observer->setLastName('obsLast');
        $observer->setUsername('observer');
        $observer->setEmail('ces55739@bethel.edu');
        $observer->setPassword(NULL);
        $observer->addRole($observerRole);
        $observer->setEnabled(1);
        $manager->persist($observer);

        $tutor = new User();
        $tutor->setFirstName('Maddie');
        $tutor->setLastName('Flatt');
        $tutor->setUsername('maf54223');
        $tutor->setEmail('ces55739@bethel.edu');
        $tutor->setPassword(NULL);
        $tutor->addRole($tutorRole);
        $tutor->setEnabled(1);
        $manager->persist($tutor);

                $tutor = new User();
        $tutor->setFirstName('Michael');
        $tutor->setLastName('Urch');
        $tutor->setUsername('mju85733');
        $tutor->setEmail('ces55739@bethel.edu');
        $tutor->setPassword(NULL);
        $tutor->addRole($tutorRole);
        $tutor->setEnabled(1);
        $manager->persist($tutor);

                $tutor = new User();
        $tutor->setFirstName('Emily');
        $tutor->setLastName('Hammerstrom');
        $tutor->setUsername('emh88758');
        $tutor->setEmail('ces55739@bethel.edu');
        $tutor->setPassword(NULL);
        $tutor->addRole($tutorRole);
        $tutor->setEnabled(1);
        $manager->persist($tutor);

                $tutor = new User();
        $tutor->setFirstName('Courtney');
        $tutor->setLastName('Sperry');
        $tutor->setUsername('specou');
        $tutor->setEmail('ces55739@bethel.edu');
        $tutor->setPassword(NULL);
        $tutor->addRole($tutorRole);
        $tutor->setEnabled(1);
        $manager->persist($tutor);

        $student = new User();
        $student->setFirstName('Caleb');
        $student->setLastName('Schwarze');
        $student->setUsername('ces55739');
        $student->setEmail('ces55739@bethel.edu');
        $student->setPassword(NULL);
        $student->addRole($cmRole);
        $student->setEnabled(1);
        $manager->persist($student);

        $student = new User();
        $student->setFirstName('Mark');
        $student->setLastName('Engstrom');
        $student->setUsername('mwe43644');
        $student->setEmail('ces55739@bethel.edu');
        $student->setPassword(NULL);
        $student->addRole($tutorRole);
        $student->setEnabled(1);
        $manager->persist($student);

        $apiuser = new User();
        $apiuser->setFirstName('api');
        $apiuser->setLastName('user');
        $apiuser->setUsername('apiuser');
        $apiuser->setEmail('');
        $apiuser->setPassword(NULL);
        $apiuser->addRole($apiuserRole);
        $apiuser->setEnabled(1);
        $manager->persist($apiuser);
        // Create usernames from API
        // $wsapi = $this->container->get('wsapi');
        // $usernames = $wsapi->getUsernameList(200);

        // //print_r($usernames);
        // foreach ($usernames as $key => $username) {
        //     $instructor = new User();
        //     $instructor->setLab('1');
        //     $instructor->setFirstName($username . 'First');
        //     $instructor->setLastName($username . 'Last');
        //     $instructor->setUsername($username);
        //     $instructor->setEmail($username . '@bethel.edu');
        //     $instructor->setPassword(NULL);

        //     $role = substr($username, 0, 3);
        //     if ($role == "fac"){
        //         $instructor->setRoles( array(
        //                             "role" => "ROLE_INSTRUCTOR",
        //                         ));
        //     }else{
        //         $instructor->setRoles( array(
        //                             "role" => "ROLE_STUDENT",
        //                         ));
        //     }
        //     $instructor->setEnabled(1);
        //     $manager->persist($instructor);
        // }

        $manager->flush();
    }

}
