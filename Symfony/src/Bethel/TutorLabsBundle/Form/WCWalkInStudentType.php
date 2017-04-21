<?php

namespace Bethel\TutorLabsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WCWalkInStudentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('CourseCode', 'hidden', array(
                    'required' => true,
                )
            )
            ->add('CourseSection', 'hidden', array(
                    'required' => true,
                )
            )
            ->add('Assignment', 'text', array(
                    'label' => 'Assignment:',
                    'required' => true,
                    'attr' => array('style' => 'width:60%;'),
                    'attr' => array('class' => 'form-control'),
                )
            )
            ->add('Comment', 'textarea', array(
                    'label' => 'We Worked On:',
                    'required' => true,
                    'attr' => array('style' => 'width:60%; height:100px')
                )
            )
            ->add('Suggestion', 'textarea', array(
                    'label' => 'Suggestions for Further Revision:', 'required' => true,
                    'attr' => array('style' => 'width:60%; height:100px')
                )
            )
            ->add('ferpaAgreement', 'checkbox', array(
                    'label' => "Email this form to the Student's Instructor.",
                    "mapped" => false,
                    'required' => false,
                )
            )
            ->add('save', 'submit', array(
                    'label' => 'End Session',
                    'attr' => array('class' => 'btn-primary')
                )
            )
            ->add('first', 'text', array(
                    "label" => "Enter the Student's first name:",
                    "mapped" => false,
                    'attr' => array('style' => 'width:90%;'),
                    'attr' => array('class' => 'form-control'),
                )
            )
            ->add('last', 'text', array(
                    "label" => "Enter the Student's last name:",
                    "mapped" => false,
                    'attr' => array('style' => 'width:90%;'),
                    'attr' => array('class' => 'form-control'),
                )
            )
            ->add('email', 'text', array(
                    "label" => "Enter the Student's email:",
                    "mapped" => false,
                    'attr' => array('style' => 'width:60%;'),
                    'attr' => array('class' => 'form-control'),
                )
            )
            ->add('Multilingual', 'checkbox', array(
                    "label" => "This session was a Multilingual session.",
                    'required' => false,
                )
            )
            ->add('StudUsername', 'hidden', array(
                    'attr' => array(
                        'readonly' => 'readonly',
                    )
                )
            )
            ->add('id', 'hidden');

    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bethel\TutorLabsBundle\Entity\WCAppointmentData'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bethel_tutorlabsbundle_wcwcappointmentdata';
    }
}