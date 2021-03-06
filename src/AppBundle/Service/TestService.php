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
use Doctrine\ORM\Tools\Pagination\Paginator;

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
    public function getAllTest($currentPage = 1, $limit = 5, $published = false)
    {

        $repository = $this->em->getRepository('AppBundle:Test');
        if ($published == true) {
            $query = $repository->createQueryBuilder('p')
                ->where('p.published = 1')
                ->getQuery();
        } else {
            $query = $repository->createQueryBuilder('p')
                ->getQuery();
        }

        $paginator = $this->paginate($query, $currentPage, $limit);

        return $paginator;
    }
    public function paginate($dql, $page = 1, $limit = 5)
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1)) // Offset
            ->setMaxResults($limit); // Limit
        return $paginator;
    }
    public function getTests($name, $currentPage = 1, $limit = 5)
    {
        $repository = $this->em->getRepository('AppBundle:Test');
        $query = $repository->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->orWhere('p.description LIKE :name')
            ->andWhere('p.published = 1')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery();
        $paginator = $this->paginate($query, $currentPage, $limit);

        return $paginator;
    }
    public function getTestsWithoutPaginating($name)
    {
        $repository = $this->em->getRepository('AppBundle:Test');
        $query = $repository->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->orWhere('p.description LIKE :name')
            ->andWhere('p.published = 1')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery();
        /*$paginator = $this->paginate($query, $currentPage, $limit);
        return $paginator;*/


        return $query->getResult();
    }
    public function getRecentTests($count = 5)
    {

        $q = $this->em->createQuery("select u from AppBundle\Entity\Test u where u.published=1 
                                    order by u.createdAt desc ");
        $q->setMaxResults($count);
        $tests = $q->getResult();

        return $tests;
    }
    public function getMostPopularTests($count = 5)
    {

        $q = $this->em->createQuery("select u from AppBundle\Entity\Test u where u.published=1 
                                    order by u.timesStarted desc ");
        $q->setMaxResults($count);
        $tests = $q->getResult();

        return $tests;
    }
    public function getUserTests($id, $currentPage = 1, $limit = 5)
    {
        $repository = $this->em->getRepository('AppBundle:Test');
        $query = $repository->createQueryBuilder('p')
            ->where('p.user = :id')
            ->setParameter('id', $id)
            ->getQuery();
        $paginator = $this->paginate($query, $currentPage, $limit);

        return $paginator;
    }
    public function getTestQuestions($id, $currentPage = 1, $limit = 5)
    {
        /*$questions=$this->em->getRepository('AppBundle:Question')->findBy(array('test'=>$id));
        return $questions;*/
        $repository = $this->em->getRepository('AppBundle:Question');
        $query = $repository->createQueryBuilder('p')
            ->where('p.test = :id')
            ->setParameter('id', $id)
            ->getQuery();
        $paginator = $this->paginate($query, $currentPage, $limit);

        return $paginator;
    }
    public function getQuestionById($id)
    {
        $question=$this->em->getRepository('AppBundle:Question')->find($id);
        return $question;
    }
    public function getAnswerById($id)
    {
        $question=$this->em->getRepository('AppBundle:Answer')->find($id);
        return $question;
    }
    public function getQuestionAnswers($id, $currentPage = 1, $limit = 5)
    {
        /*$questions=$this->em->getRepository('AppBundle:Answer')->findBy(array('question'=>$id));
        return $questions;*/

        $repository = $this->em->getRepository('AppBundle:Answer');
        $query = $repository->createQueryBuilder('p')
            ->where('p.question = :id')
            ->setParameter('id', $id)
            ->getQuery();
        $paginator = $this->paginate($query, $currentPage, $limit);

        return $paginator;
    }
}
