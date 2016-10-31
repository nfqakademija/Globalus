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
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Form\PostType;
use AppBundle\Form\LoginType;
use AppBundle\Form\UserType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType;
class RegistrationController extends Controller
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @Route("/registration", name="app.registration")
     */
    public function createUser(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->setEnabled(false);
            // random hash used for confirmation token
            $random_hash = md5(uniqid(rand(), true));
            $user->setConfirmationToken($random_hash);
            $user->addRole('ROLE_USER');
            $this->sendAction($user,$random_hash);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app.successReg', ['id' => $user->getId()]);
        }

        return $this->render('AppBundle:Post:create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
    /**
     * @Route("/successReg/{id}", name="app.successReg")
     */
    public function showSuccessRegistration($id)
    {
        $exampleService = $this->get('app.example');
        return $this->render('AppBundle:Post:successReg.html.twig',
            [

                'postas' =>  $exampleService->getUserById($id),

            ]
        );
    }


    public function sendAction(User $user, $confirmationToken)
    {

        $message = \Swift_Message::newInstance()
            ->setSubject('Pabaikite registraciją')
            ->setFrom('nfqglobalus@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'AppBundle:Email:registration.html.twig',
                    array('name' => $user->getUsername(),
                        'token' => $confirmationToken)
                ),
                'text/html'
            );
        $this->get('swiftmailer.mailer.default')->send($message);
        return $this->render('@App/Home/index.html.twig');
    }

    /**
     * @Route("/confirmUser/{confirmationToken}")
     */
    public function confirmUser($confirmationToken){
        $exampleService = $this->get('app.example');
        $user = $exampleService->enableUser($confirmationToken);
        if($user==null){
            //TODO implement error page
            echo "klaida";
        }else {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->render('@App/Post/createdUser.html.twig');
        }
    }
    /**
     * @Route("/sendReset", name="app.sendReset")
     */
    public function sendReset(Request $request){
        $user = new User();
        $exampleService = $this->get('app.example');
        //$email = array();
        $form = $this->createForm(SendEmailPswResetType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $email=$user->getEmail();
            //throw new Exception();
            $random_hash = md5(uniqid(rand(), true));
            $user=$exampleService->getUserByEmail($email);

            $user->setConfirmationToken($random_hash);
            //$this->sendAction($user,$random_hash);
            $message = \Swift_Message::newInstance()
                ->setSubject('Pabaikite registraciją')
                ->setFrom('nfqglobalus@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'AppBundle:Email:reset.html.twig',
                        array('name' => $user->getUsername(),
                            'token' => $random_hash)
                    ),
                    'text/html'
                );
            $this->get('swiftmailer.mailer.default')->send($message);


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            ///TODO change return and form valiation
            return $this->redirectToRoute('app.successReg', ['id' => $user->getId()]);
        }

        return $this->render('AppBundle:Post:create.html.twig',
            [
                'form' => $form->createView()
            ]
        );



    }
    /**
     * @Route("/resetPassword/{confirmationToken}")
     */
    public function resetPassword(Request $request,$confirmationToken){
        $exampleService = $this->get('app.example');
        $password = "";
        $user = new User();
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user= $form->getData();

            $password = $user->getPassword();
            $user = $exampleService->changePassword($password,$confirmationToken);


            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            ///TODO change return and form valiation
            return $this->redirectToRoute('app.successReg', ['id' => $user->getId()]);
        }

        return $this->render('AppBundle:Post:create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}