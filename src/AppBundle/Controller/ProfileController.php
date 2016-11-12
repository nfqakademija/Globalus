<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.31
 * Time: 23.20
 */

namespace AppBundle\Controller;


use AppBundle\Service\ExampleService;
use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ProfileController extends Controller
{
    /**
     * @Route("/", name="profile")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Profile:index.html.twig', []);
    }

    /**
     * @Route("/changePassword", name="changePassword")
     */
    public function changePassword(Request $request)
    {
        $user = new User();
        $profileService = $this->get('app.user');
        //$email = array();
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $password = $user->getPlainPassword();

            /** @var User $user */
            $user = $this->getUser();
            
            $user = $profileService->getUserById($user->getId(), $password);
            $user->setPlainPassword($password);

            $userManager = $this->container->get('fos_user.user_manager');

            $userManager->updateUser($user, true);


            ///TODO validation
            return $this->redirectToRoute('app.successPassChange', ['id' => $user->getId()]);
        }

        return $this->render('AppBundle:Profile:create.html.twig',
            [
                'form' => $form->createView()
            ]
        );

    }
    /**
     * @Route("/successPassChange/{id}", name="app.successPassChange")
     */
    public function showSuccessPassChange($id)
    {
        $userService = $this->get('app.user');
        return $this->render('AppBundle:Profile:successPassChange.html.twig',
            [
                'user' => $userService->getUserById($id),
            ]
        );
    }
}
