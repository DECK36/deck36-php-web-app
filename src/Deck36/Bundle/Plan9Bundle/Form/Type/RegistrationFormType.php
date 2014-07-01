<?php
/**
 * Declares the RegistrationFormType class.
 *
 * @author     Mike Lohmann <mike.lohmann@deck36.de>
 */

namespace Deck36\Bundle\Plan9Bundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

/**
 * Class RegistrationFormType
 *
 * @package Deck36\Bundle\Plan9Bundle\Form\Type
 */
class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        //$builder->add('name', null, array('label' => 'Playername:'));
    }

    public function getName()
    {
        return 'deck36_plan9_form_type_user_registration';
    }
}