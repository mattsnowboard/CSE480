<?php

namespace Wws\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Register Form Builder
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
class RegisterType extends AbstractType
{
    public function getName()
    {
        return 'register';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username', 'text', array(
            'label' => 'Username:'
        ));
        $builder->add('email', 'email', array(
            'label' => 'Email:'
        ));
        $builder->add('password', 'password', array(
            'label' => 'Password:'
        ));
        $builder->add('first_name', 'text', array(
            'label' => 'First Name:'
        ));
        $builder->add('last_name', 'text', array(
            'label' => 'Last Name:'
        ));
        $builder->add('birthdate', 'birthday', array(
            'label' => 'Birthdate:'
        ));
        $builder->add('city', 'text', array(
            'label' => 'City:'
        ));
        $builder->add('state', 'text', array(
            'label' => 'State:'
        ));
        $builder->add('country', 'text', array(
            'label' => 'Country:'
        ));
    }

    public function getDefaultOptions(array $options)
    {
        $options = array_merge(array(
            'validation_constraint' => new Assert\Collection(array(
                'fields' => array(
                    'username' => new Assert\NotBlank(),
                    'email'    => new Assert\Email(),
                    'password' => new Assert\NotBlank(),
                    'first_name' => new Assert\NotBlank(),
                    'last_name'  => new Assert\NotBlank(),
                    'birthdate' => new Assert\NotBlank(),
                    'city' => new Assert\NotBlank(),
                    'state'    => new Assert\NotBlank(),
                    'country' => new Assert\NotBlank(),
                ),
            ))
        ), $options);

        return $options;
    }
}