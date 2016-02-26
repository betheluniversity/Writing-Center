<?php

namespace Bethel\UserBundle\Form\Type;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('lab');
        $builder->add('roles');
    }



    public function getName(){
        return 'bethel_user_registration';
    }

    public function getLab()
    {
        return 'bethel_user_registration';
    }

    public function getRoles()
    {
        return 'bethel_user_registration';
    }
}