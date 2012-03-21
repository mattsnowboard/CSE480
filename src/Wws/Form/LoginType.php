<?php

namespace Wws\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Login Form Builder
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 */
class LoginType extends AbstractType
{
    public function getName()
    {
        return 'login';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('username', 'text', array(
            'label' => 'Username:'
        ));
        $builder->add('password', 'password', array(
            'label' => 'Password:'
        ));
    }

    public function getDefaultOptions(array $options)
    {
        $options = array_merge(array(
            'validation_constraint' => new Assert\Collection(array(
                'fields' => array(
                    'username' => new Assert\NotBlank(),
                    'password' => new Assert\NotBlank(),
                ),
            ))
        ), $options);

        return $options;
    }
}