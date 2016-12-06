<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 19.33
 */

namespace AppBundle\Controller;

use AppBundle\Form\ResetPasswordType;
use AppBundle\Form\SendEmailPswResetType;
use FOS\UserBundle\FOSUserBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\User;
use AppBundle\Event\Events;
use AppBundle\Form\RegistrationType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use ReCaptcha\ReCaptcha;

class RegistrationController extends Controller
{
    /**
     * @Route("/registration", name="app.registration")
     */
    public function createUser(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setEnabled(false);
            $userService = $this->get('app.user');
            if ($userService->getUserByEmail($user->getEmail()) != null) {
                return $this->render('AppBundle:LoginRegistration:create.html.twig', [
                        'form' => $form->createView(),
                        'error' => 'Toks Vartotojas jau yra užregistruotas'
                    ]);
            }
            $password = $user->getPlainPassword();


            if (strlen($password) < '8' || strlen($password) > '32') {
                return $this->render('AppBundle:LoginRegistration:create.html.twig', [
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi būti didesnis tarp 8 ir 32 simbolių'
                    ]);
            } elseif (!preg_match("#[0-9]+#", $password)) {
                return $this->render('AppBundle:LoginRegistration:create.html.twig', [
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną numerį'
                    ]);
            } elseif (!preg_match("#[A-Z]+#", $password)) {
                return $this->render('AppBundle:LoginRegistration:create.html.twig', [
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną didžiąją raidę'
                    ]);
            } elseif (!preg_match("#[a-z]+#", $password)) {
                return $this->render('AppBundle:LoginRegistration:create.html.twig', [
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną mažąją raidę'
                    ]);
            }

            $recaptcha = new ReCaptcha('6LfgEwsUAAAAAFPhwhOUMu5V_PthvwTa42jhxfSe');
            $resp = $recaptcha->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());

            if (!$resp->isSuccess()) {
                return $this->render('AppBundle:LoginRegistration:create.html.twig', [
                        'form' => $form->createView(),
                        'error' => 'Nepatvirtinote, kad nesate robotas'
                    ]);
            }
            /*else{
                // Everything works good ;)
            };*/
            // random hash used for confirmation token
            $random_hash = md5(uniqid(rand(), true));
            $user->setConfirmationToken($random_hash);
            $user->addRole('ROLE_USER');
            $user->setUsername($user->getEmail());
            //$this->sendAction($user, $random_hash);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // sending email
            $emailSender = $this->container->get('app.email_send');
            $emailSender->send($user, $random_hash, 'create');
            return $this->redirectToRoute('app.successReg', ['id' => $user->getId()]);
        }

        return $this->render('AppBundle:LoginRegistration:create.html.twig', [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/successReg/{id}", name="app.successReg")
     */
    public function showSuccessRegistration($id)
    {
        $userService = $this->get('app.user');
        return $this->render('AppBundle:LoginRegistration:successReg.html.twig', [
                'user' => $userService->getUserById($id),
            ]);
    }

    /**
     * @Route("/confirmUser/{confirmationToken}", name="app.confirmUser")
     */
    public function confirmUser($confirmationToken)
    {
        $userService = $this->get('app.user');
        $user = $userService->enableUser($confirmationToken);
        if ($user == null) {
            return $this->render('@App/LoginRegistration/createdUser.html.twig', [
                'error' => 'Nėra tokio vartotojo arba neteisingas puslapis'
            ]);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->render('@App/LoginRegistration/createdUser.html.twig');
        }
    }

    /**
     * @Route("/sendReset", name="app.sendReset")
     */
    public function sendReset(Request $request)
    {
        $user = new User();
        $exampleService = $this->get('app.user');

        $form = $this->createForm(SendEmailPswResetType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $email = $user->getEmail();

            $random_hash = md5(uniqid(rand(), true));
            $user = $exampleService->getUserByEmail($email);

            $user->setConfirmationToken($random_hash);
            //$this->sendAction($user,$random_hash);
            $emailSender = $this->container->get('app.email_send');
            $emailSender->send($user, $random_hash, 'reset');



            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute('app.successSendReset', ['id' => $user->getId()]);
        }
        return $this->render('AppBundle:LoginRegistration:createSendReset.html.twig', [
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/successSendReset/{id}", name="app.successSendReset")
     */
    public function showSuccessSendReset($id)
    {
        $exampleService = $this->get('app.user');
        return $this->render('AppBundle:LoginRegistration:successSendReset.html.twig', [
                'user' => $exampleService->getUserById($id),
            ]);
    }

    /**
     * @Route("/resetPassword/{confirmationToken}", name="app.resetPassword")
     */
    public function resetPassword(Request $request, $confirmationToken)
    {
        $userService = $this->get('app.user');
        $mainUser = $userService->findUserByConfirmToken($confirmationToken);
        if ($mainUser == null) {
            return $this->render('@App/LoginRegistration/createdUser.html.twig', [
                'error' => 'Nėra tokio vartotojo arba neteisingas puslapis']);
        } else {
            $user = new User();
            $form = $this->createForm(ResetPasswordType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                /** @var User $user */
                $user = $form->getData();
                $password = $user->getPassword();
                if (strlen($password) < '8' || strlen($password) > '32') {
                    return $this->render('AppBundle:LoginRegistration:createReset.html.twig', [
                            'form' => $form->createView(),
                            'error' => 'Slaptažodis turi būti didesnis tarp 8 ir 32 simbolių'
                        ]);
                } elseif (!preg_match("#[0-9]+#", $password)) {
                    return $this->render('AppBundle:LoginRegistration:createReset.html.twig', [
                            'form' => $form->createView(),
                            'error' => 'Slaptažodis turi turėti bent vieną numerį'
                        ]);
                } elseif (!preg_match("#[A-Z]+#", $password)) {
                    return $this->render('AppBundle:LoginRegistration:createReset.html.twig', [
                            'form' => $form->createView(),
                            'error' => 'Slaptažodis turi turėti bent vieną didžiąją raidę'
                        ]);
                } elseif (!preg_match("#[a-z]+#", $password)) {
                    return $this->render('AppBundle:LoginRegistration:createReset.html.twig', [
                            'form' => $form->createView(),
                            'error' => 'Slaptažodis turi turėti bent vieną mažąją raidę'
                        ]);
                }
                $user = $userService->changePassword($password, $confirmationToken);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('app.successReset', ['id' => $user->getId()]);
            }

            return $this->render('AppBundle:LoginRegistration:createReset.html.twig', [
                    'form' => $form->createView()
                ]);
        }
    }

    /**
     * @Route("/successReset/{id}", name="app.successReset")
     */
    public function showSuccessReset($id)
    {
        $userService = $this->get('app.user');
        return $this->render('AppBundle:LoginRegistration:successReset.html.twig', [
                'user' => $userService->getUserById($id),
            ]);
    }
}
