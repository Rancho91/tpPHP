<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use AppBundle\Entity\Products;
use AppBundle\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class productType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('image', FileType::class, array('attr'=>array('onchange'=>'onChange(event)')))
            ->add('categories', EntityType::class, [
                'class' => 'AppBundle\Entity\Categories',
                'choice_label' => 'name',
                'placeholder' => 'Seleccione una categoría',
            ])
            ->add('save', SubmitType::class,array('label'=>'Nuevo Producto'));
    }
}
