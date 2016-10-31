<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class ExampleService
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Get posts from db
     *
     * @return \AppBundle\Entity\Post[]|array
     */
    public function getPostsFromDb()
    {
        $repository = $this->em->getRepository('AppBundle:Post');
        $posts = $repository->findAll();

        return $posts;
    }
    public function getPostsById($id)
    {
        $repository = $this->em->getRepository('AppBundle:Post');
        $posts = $repository->find($id);

        return $posts;
    }
    public function getUserById($id)
    {
        $repository = $this->em->getRepository('AppBundle:User');
        $posts = $repository->find($id);

        return $posts;
    }
}