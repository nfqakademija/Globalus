<?php
/**
 * Created by PhpStorm.
 * User: dziugas
 * Date: 16.12.1
 * Time: 16.02
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact")
     */

    public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $contact = new Contact();
//        $contact->setTask('Write a blog post');
//        $contact->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($contact)
            ->add('name', TextType::class, array('label' => 'Vardas'))
            ->add('email', EmailType::class, array('label' => 'E. Paštas'))
            ->add('text', TextareaType::class, array('label' => 'Komentaras'))
            ->add('save', SubmitType::class, array('label' => 'Siųsti komentarą'))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();
            $em = $this->getDoctrine()->getManager();


            $em->persist($contact);
            $em->flush();

            return $this->render('AppBundle:Contact:success.html.twig',[]);
        }

        return $this->render('AppBundle:Contact:contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}