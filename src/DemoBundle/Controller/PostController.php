<?php

namespace DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PostController extends Controller
{
    /**
     * @Route("/")
     */
    public function listAction()
    {
        $repo = $this->getDoctrine()->getManager()
            ->getRepository('DummyBundle:Post');
        $posts = $repo->findAll();
        return $this->render(
            'DummyBundle:Post:list.html.twig',
            ['posts' => $posts]
        );
    }

    /**
     * @Route("/show/{id}", name="app.show_post")
     */
    public function showAction($id)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('DummyBundle:Post');
        $post = $repo->find($id);
        return $this->render('DummyBundle:Post:show.html.twig',
            [
                'post' => $post,
            ]
        );
    }


    /**
     * @Route("/create")
     */
    public function createAction(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $post */
            $post = $form->getData();
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app.show_post', ['id' => $post->getId()]);
        }

        return $this->render('DummyBundle:Post:create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }


}
