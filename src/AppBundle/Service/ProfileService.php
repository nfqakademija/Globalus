<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.31
 * Time: 23.35
 */

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
class ProfileService
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getUserById($id,$password)
    {

        $repository = $this->em->getRepository('AppBundle:User');
        $posts = $repository->find($id);
        $posts->setPlainPassword($password);
        $q = $this->em->createQuery("select u from AppBundle\Entity\User u where u.id=$id");
        $users = $q->getResult();
        foreach ($users as $user){
            $pass = $user->getPassword();
        }

       // $q = $this->em->createQuery("update AppBundle\Entity\User u set u.password=$pass where u.id=$id");
        //throw new Exception();
        return $posts;
    }
}