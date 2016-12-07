<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class TestStartType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $btnClass = "btn btn-success btn-lg center-block";
        $btnLabel = "Pradėti testą";
        if ($options['data']['pass']==true) {
            $builder->add('password', PasswordType::class, array('label' => 'Slaptažodis'));
        }
        $builder->add('start', SubmitType::class, array('label' => $btnLabel, 'attr' =>
            array('class' => $btnClass." top-buffer text-center")));
    }
}
