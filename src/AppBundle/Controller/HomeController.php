<?php

namespace AppBundle\Controller;

use phpDocumentor\Reflection\Types\Null_;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Response;

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
        $recentTests = $this->get('app.tests')->getRecentTests();
        $popularTest = $this->get('app.tests')->getMostPopularTests();
        if ($form->isSubmitted() && $form->isValid()) {
            $data=$form->getData();
            if (preg_match('/[\W]+/', $data)) {
                return $this->render('AppBundle:Home:index.html.twig', [
                    'form' => $form->createView(),
                    'testsPop' => $popularTest,
                    'tests' => $recentTests,
                    'error' => 'Negalima ieškoti su simboliais'
                ]);
            }
            return $this->redirectToRoute('searchByName', array('name' => $data));
        }

        return $this->render('AppBundle:Home:index.html.twig', [
            'form' => $form->createView(),
            'testsPop' => $popularTest,
            'tests' => $recentTests
        ]);
    }

    /**
     * @Route("/search/{page}", name="search")
     */
    public function searchAction(Request $request, $page = 1)
    {

        $limit = 10;
        $tests = $this->get('app.tests')->getAllTest($page, $limit, true);

        $maxPages = ceil($tests->count() / $limit);
        $thisPage = $page;
        if ($request->isMethod('POST')) {
            $searchParameter = $request->request->get('id');

            $tests = $this->get('app.tests')->getTests($searchParameter, $page, $limit);


            $maxPages = ceil($tests->count() / $limit);
            $thisPage = $page;
            $tests = $this->get('app.tests')->getTestsWithoutPaginating($searchParameter);

            $status = 'error';
            $html = '';
            if ($tests) {
                $data = $this->render('AppBundle:Home:list.html.twig', array(
                    'tests' => $tests,
                    'maxPages' => $maxPages,
                    'thisPage' => $thisPage
                ));
                $status = 'success';
                $html = $data->getContent();
            }


            $jsonArray = array(
                'status' => $status,
                'data' => $html,
            );

            $response = new Response(json_encode($jsonArray));
            $response->headers->set('Content-Type', 'application/json; charset=utf-8');

            return $response;
        }


        return $this->render('AppBundle:Home:search.html.twig', [

            'tests' => $tests,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }

    /**
     * @Route("/search/name/{name}/{page}", name="searchByName")
     */
    public function searchByNameAction($name, $page = 1)
    {
        $limit = 10;
        $tests=$this->get('app.tests')->getTests($name, $page, $limit);
        $maxPages = ceil($tests->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Home:search.html.twig', [
            'tests' => $tests,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }
    /**
     * @Route("/about", name="about")
     */
    public function aboutPage()
    {
        return $this->render('AppBundle:Home:about.html.twig', [  ]);
    }
    /**
     * @Route("/tests/popular", name="popularTests")
     */
    public function mostPopularTest()
    {
        $popularTest = $this->get('app.tests')->getMostPopularTests(10);
        return $this->render('AppBundle:Home:popNewTests.html.twig', [
            'tests' => $popularTest,
            'title' => 'Populiariausi testai'
        ]);
    }
    /**
     * @Route("/tests/new", name="newTests")
     */
    public function newTest()
    {
        $recentTests = $this->get('app.tests')->getRecentTests(10);
        return $this->render('AppBundle:Home:popNewTests.html.twig', [
            'tests' => $recentTests,
            'title' => 'Naujausi testai'
        ]);
    }
}
