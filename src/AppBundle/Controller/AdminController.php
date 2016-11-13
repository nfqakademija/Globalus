<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.12
 * Time: 18.47
 */

namespace AppBundle\Controller;
use AppBundle\Form\ChangeRolesType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller
{
    /**
     * @Route("/", name="adminHome")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/user", name="users")
     */
    public function userAction()
    {
        $userService = $this->get('app.user');
        $users = $userService->getAllUsers($this->getUser());
        return $this->render('AppBundle:Admin:user.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/user/{id}", name="userAction")
     */
    public function userActionList($id, Request $request)
    {
        $userService = $this->get('app.user');
        $user = $userService->getUserById($id);
        $roles=null;
        $form = $this->createForm(ChangeRolesType::class, $roles);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->removeRole('ROLE_USER');
            $user->removeRole('ROLE_ADMIN');
            $user->removeRole('ROLE_SUPER_ADMIN');

            $deleteMessages = $request->get('change_roles');
            foreach($deleteMessages  as $deleteMessageId) {
                //Do something with the ID

                if(substr($deleteMessageId, 0, 4)=="ROLE"){


                        $user->addRole($deleteMessageId);
                    

                }

            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->render('AppBundle:Admin:index.html.twig', []);
        }
        return $this->render('AppBundle:Admin:userAction.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/user/delete/{id}", name="userDelete")
     */
    public function userDelete($id)
    {
        $userService = $this->get('app.user');

        $user = $userService->getUserById($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
}