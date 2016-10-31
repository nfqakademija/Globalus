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
     * @Route("/changePassword")
     */
    public function changePassword(Request $request)
    {
        $user = new User();
        $profileService = $this->get('app.profile');
        //$email = array();
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $password=$user->getPlainPassword();

            /** @var User $user */
            $user = $this->getUser();
            //fos_user.security.login_manager
            $user=$profileService->getUserById($user->getId(), $password);
            //$user->setPlainPassword($password);
            $user->addRole("ROLE_ADMIN");
            /*
             *
echo $user->getEmail();
throw new Exception();*/

            // nepakeičia psw
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->refresh($user);
            $em->flush();

            ///TODO change return
            return $this->redirectToRoute('app.successReg', ['id' => $user->getId()]);
        }

        return $this->render('AppBundle:Post:create.html.twig',
            [
                'form' => $form->createView()
            ]
        );

    }
}