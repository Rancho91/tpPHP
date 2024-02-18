<?php
namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class categoriesFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categories', EntityType::class, [
            'class' => 'AppBundle\Entity\Categories',
            'choice_label' => 'name',
            'placeholder' => 'Seleccione una categoría',
            'required' => false,
        ]) ->add('save', SubmitType::class, array('label' => 'Buscar'));
        ;
    }
}