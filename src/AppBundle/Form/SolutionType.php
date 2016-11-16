<?php
namespace AppBundle\Form;use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Form\Type\RegistrationFormType;

class SolutionType extends AbstractType
{
    /**
 * @param FormBuilderInterface $builder
 * @param array $options
 */
    public function buildForm(FormBuilderInterface $builder, array $options)//Laukelis
    {
        $entity = $builder->getData();

        $builder->add('text', CheckboxType::class, ['label' => $entity->getText()]);
    }    /**
 * @param OptionsResolver $resolver
 */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Answer'
        ));
    }
}