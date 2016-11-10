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
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u ");
        $users = $q->getResult();
        foreach ($users as $user){
            if($user->getEmail()===$email)
            {
                return $user;
            }
        }
        $posts = $repository->findBy(array("email"=>$email));

        return $posts;
    }
    public function enableUser($confirmationToken){
        $repository = $this->em->getRepository('AppBundle:User');
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u ");
        $users = $q->getResult();
        foreach ($users as $user){
            if($user->getConfirmationToken()===$confirmationToken)
            {
                $user->setEnabled(true);
                $user->setConfirmationToken(null);
                return $user;
            }
        }

        return null;
    }
    public function changePassword($password,$confirmationToken){
        $repository = $this->em->getRepository('AppBundle:User');
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u ");
        $users = $q->getResult();
        foreach ($users as $user){
            if($user->getConfirmationToken()===$confirmationToken)
            {
                $user->setEnabled(true);
                $user->setConfirmationToken(null);
                $user->setPlainPassword($password);
                return $user;
            }
        }

        /**/
        //throw new Exception();
        return null;
    }
}
