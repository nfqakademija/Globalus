<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 19.33
 */

namespace AppBundle\Controller;


use AppBundle\Mailer\Mailer;
use FOS\UserBundle\FOSUserBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use AppBundle\Form\PostType;
use AppBundle\Form\LoginType;
use AppBundle\Form\UserType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType;
class PostController extends Controller
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @Route("/list")
     */
    public function listAction()
    {
        $repo = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Post');
        $posts = $repo->findAll();
        /*$exampleService = $this->get('app.example');

        $posts = $exampleService->getPostsFromDb();*/

        return $this->render(
            'AppBundle:Post:list.html.twig',
            ['posts' => $posts]
        );

    }


    /**
     * @Route("/show/{id}", name="app.show_post")
     */
    public function showAction($id)
    {
        $exampleService = $this->get('app.example');

        $posts = $exampleService->getPostsFromDb();// getPostsById($id);
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
        $post = $repo->find($id);
        return $this->render('AppBundle:Post:show.html.twig',
            [
                'posts' => $posts,
                'postas' =>  $exampleService->getPostsById($id),
                'postai' =>$id
            ]
        );
    }
    /**
     * @Route("/create")
     */
    public function createAction(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $post */
            $post = $form->getData();
            $post->setUser($this->getUser());
            $post->setCreatedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app.show_post', ['id' => $post->getId()]);
        }

        return $this->render('AppBundle:Post:create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
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
           /* $user->setUsername($user->getUserName());
            $user->setEmail($user->getEmail());
            $user->setPlainPassword($user->getPassword())*/
            //TODO send email
            $user->setEnabled(false);
            $random_hash = md5(uniqid(rand(), true));
            $user->setConfirmationToken($random_hash);
            //$user->addRole('ROLE_ADMIN');
            $user->addRole('ROLE_USER');
            $this->sendAction($user,$random_hash);
            //$this->eventDispatcher->dispatch("app.create_user");
            //$user->setCreatedAt(new \DateTime());
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

        $posts = $exampleService->getPostsFromDb();// getPostsById($id);
        $repo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
        $post = $repo->find($id);
        /*$mailer = Mailer();

        $mailer->sendConfirmationEmailMessage($post);*/
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
                // app/Resources/views/Emails/registration.html.twig
                    'AppBundle:Email:registration.html.twig',
                    array('name' => $user->getUsername(),
                        'token' => $confirmationToken)
                ),
                'text/html'
            )

        ;
        /*$this->container->get('swiftmailer.email_sender.listener');
        $this->get('swiftmailer.mailer.abstract');*/
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
            echo "klaida";
        }else {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            //$em->refresh($user);
            $em->flush();
            return $this->render('@App/Post/createdUser.html.twig');
        }
    }
}