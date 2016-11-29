<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use AppBundle\Service\TestService;
class UserService
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }


    public function getUserById($id)
    {
        $repository = $this->em->getRepository('AppBundle:User');
        $posts = $repository->find($id);

        return $posts;
    }
    public function getUserByEmail($email)
    {
        $repository = $this->em->getRepository('AppBundle:User');
        $user = $repository->findOneByEmail($email);
        return $user;

    }
    public function enableUser($confirmationToken){
        $repository = $this->em->getRepository('AppBundle:User');
        $user = $repository->findOneBy(['confirmationToken' => $confirmationToken]);
        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        return $user;

    }
    public function changePassword($password,$confirmationToken){
        $repository = $this->em->getRepository('AppBundle:User');
        $user = $repository->findOneBy(['confirmationToken' => $confirmationToken]);
        $user->setConfirmationToken(null);
        $user->setPlainPassword($password);
        return $user;

    }
    public function findUserByConfirmToken($confirmationToken){
        $repository = $this->em->getRepository('AppBundle:User');
        $user = $repository->findOneBy(['confirmationToken' => $confirmationToken]);
        return $user;

    }
    public function getAllUsers($currentPage = 1,$limit = 5)
    {
        /*$repository = $this->em->getRepository('AppBundle:User');
        $users = $repository->findAll();
        return $users;*/

        $repository = $this->em->getRepository('AppBundle:User');
        $query = $repository->createQueryBuilder('p')
            ->getQuery();
        $testService = new TestService($this->em);
        $paginator = $testService->paginate($query, $currentPage,$limit);

        return $paginator;
    }
    public function getAllUsersASC()
    {
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u order by u.email asc");
        $users = $q->getResult();
        return $users;
    }
    public function getAllUsersDESC()
    {
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u order by u.email desc");
        $users = $q->getResult();
        return $users;
    }

}
