<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Test;
use AppBundle\Form\AnswerType;
use AppBundle\Form\QuestionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Faker\Provider\DateTime;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    /**
     * @Route("/test/create", name="testCreate")
     */
    public function createAction(Request $request)
    {

        $test = new Test();

        $form = $this->createFormBuilder($test)
            ->add('name', TextType::class, [
                'label' => 'Pavadinimas'
            ])
            ->add('description', TextType::class, [
                'label' => 'Aprasymas'
            ])
            ->add('questions', CollectionType::class, [
                'label' => 'Klausimai',
                'entry_type' => QuestionType::class,
                'allow_add' => true,
                'by_reference' => false,
                'prototype_name' => '__q_name__',
            ])

            ->add('save', SubmitType::class, array('label' => 'Sukurti'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $test = $form->getData();
            $em = $this->getDoctrine()->getManager();

            foreach ($test->getQuestions() as $question){
                foreach ($question->getAnswers() as $answer){
                    $answer->setQuestion($question);
                    $em->persist($answer);
                }
                $question->setTest($test);
                $em->persist($question);
            }
            $em->persist($test);
            $em->flush();

            return $this->render('AppBundle:Test:success.html.twig',[]);
        }

        return $this->render('AppBundle:Test:create.html.twig', [
            'form' => $form->createView(),
        ]);


    }
    /**
     * @Route("/create/tests" , name="createTest")
     */
    public function createTest(Request $request){
        $test = new Test();

        $form = $this->createFormBuilder($test)
            ->add('name', TextType::class, [
                'label' => 'Pavadinimas'
            ])
            ->add('description', TextType::class, [
                'label' => 'Aprasymas'
            ])
            ->add('save', SubmitType::class, array('label' => 'Sukurti'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $test = $form->getData();
            $now=new \DateTime();
            $now->format('Y-m-d H:i:s');
            $test->setCreatedAt($now);
            $test->setUser($this->getUser());
            $test->setTimesStarted(0);
            $em = $this->getDoctrine()->getManager();

            $em->persist($test);
            $em->flush();

            return $this->render('AppBundle:Test:success.html.twig',[]);
        }

        return $this->render('AppBundle:Profile:createTest.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}