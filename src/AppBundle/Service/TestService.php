<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.20
 * Time: 10.27
 */

namespace AppBundle\Service;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

class TestService
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getTestById($id)
    {
        $repository = $this->em->getRepository('AppBundle:Test');
        $posts = $repository->find($id);

        return $posts;
    }
    public function getTests($name){

        $repository = $this->em->getRepository('AppBundle:Test');
        $query = $repository->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->orWhere('p.description LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery();
        $tests_result = $query->getResult();


        return $tests_result;
    }
    public function getRecentTests($count = 5){

        $q = $this->em->createQuery("select u from AppBundle\Entity\Test u order by u.createdAt desc ");
        $q->setMaxResults($count);
        $tests = $q->getResult();

        return $tests;
    }
    public function getMostPopularTests($count = 5){

        $q = $this->em->createQuery("select u from AppBundle\Entity\Test u order by u.timesStarted desc ");
        $q->setMaxResults($count);
        $tests = $q->getResult();

        return $tests;
    }
}
