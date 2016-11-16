<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Solution;
use AppBundle\Entity\Test;
use AppBundle\Form\SolutionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilder;

class TestController extends Controller
{
    /**
     * @Route("/test/solve/{id}")
     */
    public function SolveAction($id)
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var Test $test */
        $test = $manager->getRepository("AppBundle:Test")->find($id);

        $solution = new Solution();
        //$solution->setAnswers($test->getQuestions()->first()->getAnswers());
        $solution->setTest($test);
        $solution->addAnswer($test->getQuestions()->first()->getAnswers()->first());

        $builder = $this->createFormBuilder($solution);
        $builder->add('answers', CollectionType::class);

        $form = $this->createFormBuilder($solution)
            ->add('answers', CollectionType::class) //, ["entry_type" => SolutionType::class]
            ->add('save', SubmitType::class, array('label' => 'Atsakyti'))
            ->getForm();

        $builder->

        return $this->render('AppBundle:Test:solve.html.twig', array(
            "test" => $test,
            "form" => $form->createView()
        ));
    }

}
