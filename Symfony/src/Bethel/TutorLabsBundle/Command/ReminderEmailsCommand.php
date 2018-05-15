<?php
namespace Bethel\TutorLabsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

class ReminderEmailsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('reminder-emails')
            ->setDescription('Sends reminder emails to all students with an appointment the next day.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getContainer()->get('wchelper');

        $tomorrow = new DateTime('tomorrow');
        $tomorrow = $tomorrow->format("Y-m-d");
        echo "Start emailing for appointments on ".$tomorrow.".\n";

        // Get all appts tomorrow
        $repository = $helper->getWCAppointmentRepository();
        $query = $repository->createQueryBuilder('p')
            ->where('p.StartTime LIKE :tomorrow')
            ->setParameter(':tomorrow', $tomorrow.'%')
            ->getQuery();
        $data1 = $query->getResult();

        // For each appointment tomorrow, send an email to the student.
        foreach( $data1 as $appt){
            $repository = $helper->getUserRepository();
            $query = $repository->createQueryBuilder('p')
                ->where('p.username = :StudUsername')
                ->setParameter(':StudUsername', $appt->getStudUsername())
                ->getQuery();
            $data2 = $query->getResult();
            $data2 = $data2[0];

            $to = $data2->getEmail();
            $subject = "Writing Center -- Reminder";
            
            $body = "Thank you for signing up for an appointment with the Writing Center. Here are the details of your appointment tomorrow:\n\n";
            $body .= "Tutor: " . $helper->getFirstLastNameByUsername($appt->getTutorUsername()) . "\nStart Time: ".$appt->getStartTime()->format("m/d/Y g:i a")."\nEnd Time: ".$appt->getEndTime()->format("m/d/Y g:i a")."\nLocation: Writing Center (HC 324)";
            if( $appt->getMultilingual() )
                $body .= "\nMultilingual: ".$appt->getMultilingual();

            $body .= "\n\nIf you are unable to keep this appointment, please cancel it. To cancel
your appointment, visit the Writing Center scheduling page, click on 'View
Your Scheduled Appointments,'' select the appointment from the calendar and
click 'Remove.'";

            mail( $to, $subject, $body, "From: no-reply@bethel.edu\r\n");

            $today = new DateTime();
            $today = $today->format("Y-m-d H:i:m");
            echo "Sent email to ".$data2->getEmail()." at ".$today.".\n";
        }

        echo "Done emailing.\n";
    }
}
