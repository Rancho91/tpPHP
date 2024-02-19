<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

class editPerfilType extends AbstractType
{

public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', TextType::class, array(
            'disabled' => true, 
        ))
            ->add('username', TextType::class)
            ->add('plainPassword', RepeatedType::class,array(
                'type'=>PasswordType::class,
                'first_options' =>array('label'=>'Password'),
                'second_options' =>array('label'=>'repeat Password')
            ))
            ->add('save', SubmitType::class, array(
                'label' => 'Send',
            ));
    }
}