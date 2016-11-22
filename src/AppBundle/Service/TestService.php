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
        //for mysql???
        $repository = $this->em->getRepository('AppBundle:Test');
        $query = $repository->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->orWhere('p.description LIKE :name')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery();
        $tests_result = $query->getResult();

        /*$tests=$repository->findAll();
        $counter = 0;
        foreach($tests as $test){
            if(preg_match("/['.$name.']/",$test->getName())==true)$tests_result[$counter++]=$test;
        }*/

        return $tests_result;
    }
    public function get5RecentTests(){

        $q = $this->em->createQuery("select u from AppBundle\Entity\Test u order by u.createdAt desc ");
        $q->setMaxResults(5);
        $tests = $q->getResult();

        return $tests;
    }
}
