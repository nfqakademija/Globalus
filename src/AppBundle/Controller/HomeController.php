<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        /*$user = new User();
        $profileService = $this->get('app.user');*/
        $data=null;
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();


            return $this->redirectToRoute('searchByName',array('name' => $data));
        }

        return $this->render('AppBundle:Home:index.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/search", name="search")
     */
    public function searchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tests = $em->getRepository('AppBundle:Test')->findAll();
        return $this->render('AppBundle:Home:search.html.twig', [
            'tests' => $tests
        ]);
    }

    /**
     * @Route("/search/{name}", name="searchByName")
     */
    public function searchByNameAction($name)
    {

        $tests=$this->get('app.user')->getTests($name);
        return $this->render('AppBundle:Home:search.html.twig', [
            'tests' => $tests
        ]);
    }

}
