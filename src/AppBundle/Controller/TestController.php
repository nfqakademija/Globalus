<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Test;
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
     * @Route("/testas", name="testasCreate")
     */
    public function indexAction(Request $request)
    {

        $test = new Test();

        $form = $this->createFormBuilder($test)
            ->add('questions', CollectionType::class, [
                'label' => 'Klausimas',
                'entry_type' => QuestionType::class,
                'allow_add' => true
            ])
            ->add('answer', TextType::class, ['label' => 'Atsakymas'])
            ->add('save', SubmitType::class, array('label' => 'Sukurti'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $test = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($test);
            $em->flush();

            return $this->render('AppBundle:Test:success.html.twig',[]);
        }

        return $this->render('AppBundle:Test:index.html.twig', [
            'form' => $form->createView(),
        ]);


    }


}
