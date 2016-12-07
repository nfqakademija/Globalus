<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Form\Type\RegistrationFormType;

class TestType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Pavadinimas'
            ])
            ->add('description', TextType::class, [
                'label' => 'Aprasymas'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Slaptažodis(neprivaloma)',
                'required' => false
            ])
            ->add('timeLimit', NumberType::class, [
                'label' => 'Laiko limitas(sekundėmis)'
            ])
            ->add('questionsLimit', NumberType::class, [
                'label' => 'Sprendimuose pateiktų klausimų kiekis'
            ])
            ->add('save', SubmitType::class, array('label' => 'Įrašyti'))
            ->getForm();
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Test'
        ));
    }
}
