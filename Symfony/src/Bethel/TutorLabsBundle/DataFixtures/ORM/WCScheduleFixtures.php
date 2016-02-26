<?php
namespace Bethel\TutorLabsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bethel\TutorLabsBundle\Entity\WCSchedule;

class WCScheduleFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $start = date_create('10:30 AM');
        echo date_format($start, 'g:i A');
        $end = date_create('11:00 AM');

        $timeSlot1 = new WCSchedule();
        $timeSlot1->setTimeStart($start);
        $timeSlot1->setTimeEnd($end);
        $timeSlot1->setIsActive('Yes');

        $manager->persist($timeSlot1);

        $start = date_create('12:30 PM');
        echo date_format($start, 'g:i A');
        $end = date_create('1:45 PM');

        $timeSlot2 = new WCSchedule();
        $timeSlot2->setTimeStart($start);
        $timeSlot2->setTimeEnd($end);
        $timeSlot2->setIsActive('No');

        $manager->persist($timeSlot2);

        $start = date_create('6:00 PM');
        echo date_format($start, 'g:i A');
        $end = date_create('7:00 PM');

        $timeSlot3 = new WCSchedule();
        $timeSlot3->setTimeStart($start);
        $timeSlot3->setTimeEnd($end);
        $timeSlot3->setIsActive('Yes');

        $manager->persist($timeSlot3);

        $timeSlot4 = new WCSchedule();
        $timeSlot4->setTimeStart($start);
        $timeSlot4->setTimeEnd($end);
        $timeSlot4->setIsActive('No');

        $manager->persist($timeSlot4);

        $manager->flush();
    }

}