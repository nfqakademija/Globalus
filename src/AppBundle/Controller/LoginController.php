<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{

    /**
     * @Route("/login", name="app.login")
     */
    public function showAction($id)
    {
        $exampleService = $this->get('app.example');

        $posts = $exampleService->getPostsFromDb();// getPostsById($id);
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
        $post = $repo->find($id);
        return $this->render('AppBundle:Post:show.html.twig',
            [
                'posts' => $posts,
                'postas' =>  $exampleService->getPostsById($id),
                'postai' =>$id
            ]
        );
    }
}
