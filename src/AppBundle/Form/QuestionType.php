<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Form\Type\RegistrationFormType;
class QuestionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)//Laukelis
    {
        $builder->add('text', TextType::class, ['label' => 'Klausimas']);



        $builder->add('answers', CollectionType::class, [
            'entry_type' => AnswerType::class,
            'label' => false,
            'prototype_name' => '__a_name__',
            'by_reference' => false,
            'allow_add' => true,
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Question'
        ));
    }
}