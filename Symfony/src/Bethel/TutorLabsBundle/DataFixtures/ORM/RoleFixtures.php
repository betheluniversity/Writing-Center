<?php
namespace Bethel\TutorLabsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bethel\UserBundle\Entity\Role;

class RoleFixtures implements FixtureInterface {
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager) {

        $gaRole = new Role();
        $gaRole->setName('Global Admin');
        $gaRole->setRole('ROLE_GLOBAL_ADMIN');

        $manager->persist($gaRole);

        $leadRole = new Role();
        $leadRole->setName('Administrator');
        $leadRole->setRole('ROLE_ADMIN');

        $manager->persist($leadRole);

        $cmRole = new Role();
        $cmRole->setName('Center Manager');
        $cmRole->setRole('ROLE_CENTER_MANAGER');

        $manager->persist($cmRole);

        $studentRole = new Role();
        $studentRole->setName('Student');
        $studentRole->setRole('ROLE_STUDENT');

        $manager->persist($studentRole);

        $observerRole = new Role();
        $observerRole->setName('Observer');
        $observerRole->setRole('ROLE_OBSERVER');

        $manager->persist($observerRole);

        $tutorRole = new Role();
        $tutorRole->setName('Tutor');
        $tutorRole->setRole('ROLE_TUTOR');

        $manager->persist($tutorRole);

        $apiRole = new Role();
        $apiRole->setName('API User');
        $apiRole->setRole('ROLE_API_USER');

        $manager->persist($apiRole);

        $inactive = new Role();
        $inactive->setName('Inactive');
        $inactive->setRole('ROLE_INACTIVE');

        $manager->persist($inactive);

        $manager->flush();
    }

}

?>