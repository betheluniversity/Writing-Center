<?php

namespace Bethel\TutorLabsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WCEmailPreferencesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $roleFlag
     */
    public function __construct($roleFlag)
    {
      $this->isGranted = $roleFlag;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('save', 'submit', array('label' => 'Save'))
    ;
    if($this->isGranted) {
        $builder
            ->add('subRequestEmail', 'checkbox', array('label' => 'Receive an email when a tutor requests a substitute.', 'required' => false))
            ->add('studentSignUpEmail', 'checkbox', array('label' => 'Receive an email when a student signs up for one of your shifts.', 'required' => false));
    }
}
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bethel\TutorLabsBundle\Entity\WCEmailPreferences'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bethel_tutorlabsbundle_wcemailpreferences';
    }
}
