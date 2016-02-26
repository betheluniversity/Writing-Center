<?php

namespace Bethel\TutorLabsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Bethel\TutorLabsBundle\Entity\WCSystemSettings;

class WCSystemSettingsFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
      $settings = new WCSystemSettings();
      $settings->setApptLimit(3);
      $settings->setTimeLimit(10);
      $settings->setBanLimit(2);
      $settings->setQualtricsLink("https://bethel.qualtrics.com/SE/?SID=SV_3WBRdR4d796j3WR");
      $manager->persist($settings);

      $manager->flush();
    }
}