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

        $data=null;
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();


            return $this->redirectToRoute('searchByName',array('name' => $data));
        }
        $recentTests = $this->get('app.tests')->getRecentTests();
        $popularTest = $this->get('app.tests')->getMostPopularTests();
        return $this->render('AppBundle:Home:index.html.twig', [
            'form' => $form->createView(),
            'testsPop' => $popularTest,
            'tests' => $recentTests
        ]);

    }

    /**
     * @Route("/search", name="search")
     */
    public function searchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tests = $em->getRepository('AppBundle:Test')->findby(array('published' => 1));
        return $this->render('AppBundle:Home:search.html.twig', [
            'tests' => $tests
        ]);
    }

    /**
     * @Route("/search/{name}", name="searchByName")
     */
    public function searchByNameAction($name)
    {

        $tests=$this->get('app.tests')->getTests($name);
        return $this->render('AppBundle:Home:search.html.twig', [
            'tests' => $tests
        ]);
    }
    /**
     * @Route("/about", name="about")
     */
    public function aboutPage()
    {
        return $this->render('AppBundle:Home:about.html.twig', [  ]);
    }

}
