<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Home:index.html.twig', []);
    }

    /**
     * @Route("/listd", name="posts_list")
     */
    public function listAction()
    {
        $exampleService = $this->get('app.example');

        //$posts = $exampleService->getPosts();
        $posts = $exampleService->getPostsFromDb();
        //$posts = $exampleService->getDummyPosts();
        return $this->render('AppBundle:Home:list.html.twig', [
            'posts' => $posts,
        ]);
    }
}
