<?php

namespace Wws\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Edit Profile Form Builder
 * 
 * @author Matt Durak <durakmat@msu.edu> 
 * @author Devan Sayles <saylesd1@msu.edu>
 */
class EditAdminProfileType extends AbstractType
{
    public function getName()
    {
        return 'edit_admin_profile';
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        /* Remove the username field since the user is not allowed to edit this
		
		$builder->add('username', 'text', array(
            'label' => 'Username:'
        ));
		*/
		
        $builder->add('email', 'email', array(
            'label' => 'Email:'
        ));
        $builder->add('password', 'password', array(
            'label' => 'Password:',
            'required' => false
        ));
        $builder->add('firstName', 'text', array(
            'label' => 'First Name:'
        ));
        $builder->add('last_name', 'text', array(
            'label' => 'Last Name:'
        ));
		$builder->add('phone', 'text', array(
            'label' => 'Phone Number:'
        ));

    }

    public function getDefaultOptions(array $options)
    {
        $options = array_merge(array(
            'validation_constraint' => new Assert\Collection(array(
                'fields' => array(
                    'email'    => new Assert\Email(),
                    'firstName' => new Assert\NotBlank(),
                    'last_name'  => new Assert\NotBlank(),
                    'phone' => new Assert\NotBlank(),
                ),
            ))
        ), $options);

        return $options;
    }
}