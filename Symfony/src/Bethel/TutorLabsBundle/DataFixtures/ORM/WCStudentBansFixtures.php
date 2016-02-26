<?php

namespace Bethel\TutorLabsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bethel\TutorLabsBundle\Entity\WCStudentBans;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WCStudentBansFixtures implements FixtureInterface, ContainerAwareInterface{

    /**
     * @var ContainerInterface
     */
    private $container;

    private $em;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    public function load(ObjectManager $manager){

        // $studentUser = $this->em->getRepository('BethelUserBundle:User')->findOneBy(array(
        // 'username' => 'student'
        // ));

        // $completed = date_create('2013-09-21 10:38:13');

        // $student = new WCStudentBans();
        // $student->setUsername('student');
        // $student->setbannedDate($completed);
        // $student->setUser($studentUser);
        // $manager->persist($student);

        // $manager->flush();
    }
}