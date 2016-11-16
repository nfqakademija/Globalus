<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Test;
use AppBundle\Form\AnswerType;
use AppBundle\Form\QuestionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
                'label' => 'Klausimas',
                'entry_type' => QuestionType::class,
                'allow_add' => true
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

        return $this->render('AppBundle:Test:index.html.twig', [
            'form' => $form->createView(),
        ]);


    }


}
