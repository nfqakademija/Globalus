<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

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
    public function enableUser($confirmationToken){
        $repository = $this->em->getRepository('AppBundle:User');
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u ");
        $users = $q->getResult();
        /*$repository =$this->em->createQueryBuilder();
        $user = $repository->select('*')->from('user')->where("confirmation_token ='.$confirmationToken.'");*/
        //$user = $repository->findOneBy(array('confirmation_token' => $confirmationToken));
        foreach ($users as $user){
            if($user->getConfirmationToken()===$confirmationToken)
            {
                $user->setEnabled(true);
                $user->setConfirmationToken(null);
                //$q = $this->em->createQuery("select u from AppBundle\Entity\User u ");
                return $user;
            }
        }

        /**/
        //throw new Exception();
        return null;
    }
}