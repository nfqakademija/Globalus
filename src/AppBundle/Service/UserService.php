<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

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
    public function getAllUsers($user)
    {
        $repository = $this->em->getRepository('AppBundle:User');
        $users = $repository->findAll();
        return $users;
    }
    public function getAllUsersASC($user)
    {
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u order by u.email asc");
        $users = $q->getResult();
        return $users;
    }
    public function getAllUsersDESC($user)
    {
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u order by u.email desc");
        $users = $q->getResult();
        return $users;
    }
    public function getTests($name){
        //for mysql???
        $repository = $this->em->getRepository('AppBundle:Test');
        $users=$repository->findAll();
        $counter = 0;
        foreach($users as $test){
            if(preg_match("/['.$name.']/",$test->getName())==true)$tests[$counter++]=$test;
        }

        return $tests;
    }
}
