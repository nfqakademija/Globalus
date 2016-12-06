<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Test;
use AppBundle\Entity\User;
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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class TestController extends Controller
{
    /**
     * @Route("/create/tests" , name="createTest")
     */
    public function createTest(Request $request)
    {
        $test = new Test();

        $form = $this->createFormBuilder($test)
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

            return $this->render('AppBundle:Profile:index.html.twig', []);
        }

        return $this->render('AppBundle:Profile:createTest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
