<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.31
 * Time: 22.14
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeRolesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ROLE_USER', CheckboxType::class, array('label' => 'Vartotojas',
            'value' => 'ROLE_USER','required' => ''))
        ->add('ROLE_ADMIN', CheckboxType::class, array('label' => 'Admin',
            'value' => 'ROLE_ADMIN','required' => ''))
        ->add('ROLE_SUPER_ADMIN', CheckboxType::class, array('label' => 'Super admin',
            'value' => 'ROLE_SUPER_ADMIN','required' => ''));
    }
}
