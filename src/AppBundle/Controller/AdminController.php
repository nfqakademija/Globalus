<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.12
 * Time: 18.47
 */

namespace AppBundle\Controller;
use AppBundle\Form\ChangePasswordType;
use AppBundle\Form\ChangeRolesType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Test;
use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
     * @Route("/user/{page}", name="users")
     */
    public function userAction($page = 1)
    {
        $userService = $this->get('app.user');
        $limit = 8;
        $users = $userService->getAllUsers($page,$limit);
        $maxPages = ceil($users->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Admin:user.html.twig', [
            'users' => $users,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }
    /**
     * @Route("/user/spec/{id}", name="userAction")
     */
    public function userActionList($id, Request $request)
    {
        $userService = $this->get('app.user');
        $user = $userService->getUserById($id);
        $roles=null;
        $form = $this->createForm(ChangeRolesType::class, $roles);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $request->get('change_roles');
            $numberOfSelectedRoles=0;
            foreach($roles  as $role) {
                if(substr($role, 0, 4)=="ROLE"){
                    $numberOfSelectedRoles++;
                }

            }
            if($numberOfSelectedRoles==0){
                return $this->render('AppBundle:Admin:userAction.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                    'error' => 'Nepasirinkote rolių'
                ]);
            }
            $user->removeRole('ROLE_USER');
            $user->removeRole('ROLE_ADMIN');
            $user->removeRole('ROLE_SUPER_ADMIN');


            foreach($roles  as $role) {
                //Do something with the ID

                if(substr($role, 0, 4)=="ROLE"){


                        $user->addRole($role);


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
    /**
     * @Route("/user/changePassword/{id}", name="userChangePassword")
     */
    public function userChangePassword($id, Request $request)
    {
        $userService = $this->get('app.user');
        $user = $userService->getUserById($id);
        $userType = new User();
        $form = $this->createForm(ChangePasswordType::class, $userType);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $userType->getPassword();
            if (strlen($password) < '8' || strlen($password) > '32') {
                return $this->render('AppBundle:Admin:changePassword.html.twig',
                    [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi būti didesnis tarp 8 ir 32 simbolių'
                    ]
                );
            } elseif (!preg_match("#[0-9]+#", $password)) {
                return $this->render('AppBundle:Admin:changePassword.html.twig',
                    [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną numerį'
                    ]
                );
            } elseif (!preg_match("#[A-Z]+#", $password)) {
                return $this->render('AppBundle:Admin:changePassword.html.twig',
                    [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną didžiąją raidę'
                    ]
                );
            } elseif (!preg_match("#[a-z]+#", $password)) {
                return $this->render('AppBundle:Admin:changePassword.html.twig',
                    [
                        'user' => $user,
                        'form' => $form->createView(),
                        'error' => 'Slaptažodis turi turėti bent vieną mažąją raidę'
                    ]
                );
            }

            $user->setPlainPassword($userType->getPassword());
            $userManager = $this->container->get('fos_user.user_manager');

            $userManager->updateUser($user, true);
            return $this->render('AppBundle:Admin:index.html.twig', []);
        }
        return $this->render('AppBundle:Admin:changePassword.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/userByEmailASC/{page}", name="usersbyEmailASC")
     */
    public function userActionEmailASC($page = 1)
    {
        $userService = $this->get('app.user');
        $limit = 8;
        $users = $userService->getAllUsersASC($page,$limit);
        $maxPages = ceil($users->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Admin:user.html.twig', [
            'users' => $users,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }
    /**
     * @Route("/userByEmailDESC/{page}", name="usersbyEmailDESC")
     */
    public function userActionEmailDESC($page = 1)
    {
        $userService = $this->get('app.user');
        $limit = 8;
        $users = $userService->getAllUsersDESC($page,$limit);
        $maxPages = ceil($users->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Admin:user.html.twig', [
            'users' => $users,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }
    /**
     * @Route("/tests/{page}", name="tests")
     */
    public function testsAction($page = 1){
        $testService = $this->get('app.tests');
        $limit = 5;
        $tests = $testService->getAllTest($page,$limit);
        $maxPages = ceil($tests->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Admin:tests.html.twig', [
            'tests' => $tests,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
   // print_r($tests);
        /*return $this->render('AppBundle:Admin:tests.html.twig', [
            'tests' => $tests
        ]);*/
    }
    /**
     * @Route("/tests/test/{id}/{page}", name="testsAction")
     */
    public function testActionList($id,$page =1)
    {
        $testsService = $this->get('app.tests');
        $test = $testsService->getTestById($id);
        $limit = 2;
        $questions = $testsService->getTestQuestions($id,$page,$limit);
        $maxPages = ceil($questions->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Admin:testAction.html.twig', [
            'test' => $test,
            'questions' => $questions,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }
    /**
     * @Route("/tests/delete/{id}", name="testsDelete")
     */
    public function testDelete($id)
    {
        $testsService = $this->get('app.tests');

        $test = $testsService->getTestById($id);
        $questions = $test->getQuestions();
        $em = $this->getDoctrine()->getManager();
        foreach ($questions as $question){
            $answers=$question->getAnswers();
            foreach ($answers as $answer){
                $em->remove($answer);
            }
            $em->remove($question);
        }
        $em->remove($test);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/tests/publish/{id}", name="testsPublish")
     */
    public function testPublish($id)
    {
        $testsService = $this->get('app.tests');

        $test = $testsService->getTestById($id);
        $em = $this->getDoctrine()->getManager();
        $test->setPublished(1);
        $em->persist($test);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/tests/depublish/{id}", name="testsDepublish")
     */
    public function testDepublish($id)
    {
        $testsService = $this->get('app.tests');

        $test = $testsService->getTestById($id);
        $em = $this->getDoctrine()->getManager();
        $test->setPublished(0);
        $em->persist($test);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/tests/test/question/{id}/{page}", name="questionInfo")
     */
    public function showTestQestionInfo($id,$page =1)
    {
        $testsService = $this->get('app.tests');
        //$test = $testsService->getTestById($id);
        $question = $testsService->getQuestionById($id);
        $limit = 2;
        $answers = $testsService->getQuestionAnswers($id,$page,$limit);
        $maxPages = ceil($answers->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Admin:questionAction.html.twig', [
            'question' => $question,
            'answers' => $answers,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage
        ]);
    }
    /**
     * @Route("/tests/test/questions/question/delete/{id}", name="questionDelete")
     */
    public function questionDelete($id)
    {
        $testsService = $this->get('app.tests');

        $question = $testsService->getQuestionById($id);
        $em = $this->getDoctrine()->getManager();
        $answers=$question->getAnswers();
        $em->remove($question);
        foreach ($answers as $answer){
            $em->remove($answer);
        }
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/tests/test/question/answer/delete/{id}", name="answerDelete")
     */
    public function answerDelete($id)
    {
        $testsService = $this->get('app.tests');

        $answer = $testsService->getAnswerById($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($answer);
        $em->flush();
        return $this->render('AppBundle:Admin:index.html.twig', []);
    }
    /**
     * @Route("/tests/edit/{id}", name="edit_admin_test")
     */
    public function editUserTest($id,Request $request)
    {
        $testService = $this->get('app.tests');
        $test=$testService->getTestById($id);

        $formTest = new Test();

        $form = $this->createFormBuilder($formTest)
            ->add('name', TextType::class, [
                'label' => 'Pavadinimas',
                'data' => $test->getName()
            ])
            ->add('description', TextType::class, [
                'label' => 'Aprasymas',
                'data' => $test->getDescription()
            ])
            ->add('save', SubmitType::class, array('label' => 'Įrašyti'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formTest = $form->getData();

            $test->setName($formTest->getName());
            $test->setDescription($formTest->getDescription());
            $em = $this->getDoctrine()->getManager();

            $em->persist($test);
            $em->flush();

            return $this->render('AppBundle:Admin:index.html.twig',[]);
        }

        return $this->render('AppBundle:Admin:create.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    /**
     * @Route("/tests/questions/edit/{id}", name="edit_admin_question")
     */
    public function editTestQuestion($id,Request $request)
    {
        $testService = $this->get('app.tests');
        $question=$testService->getQuestionById($id);

        $formQuestion = new Question();

        $form = $this->createFormBuilder($formQuestion)
            ->add('text', TextType::class, [
                'label' => 'Klausimas',
                'data' => $question->getText()
            ])
            ->add('save', SubmitType::class, array('label' => 'Įrašyti'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formQuestion = $form->getData();

            $question->setText($formQuestion->getText());
            $em = $this->getDoctrine()->getManager();

            $em->persist($question);
            $em->flush();

            return $this->render('AppBundle:Admin:index.html.twig',[]);
        }

        return $this->render('AppBundle:Admin:create.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    /**
     * @Route("/tests/answer/edit/{id}", name="edit_admin_answer")
     */
    public function editQuestionAnswer($id,Request $request)
    {
        $testService = $this->get('app.tests');
        $answer=$testService->getAnswerById($id);
        $formAnswer = new Answer();

        $form = $this->createFormBuilder($formAnswer)
            ->add('text', TextType::class, [
                'label' => 'Atsakymas',
                'data' => $answer->getText()
            ])
            ->add('correct',CheckboxType::class,[
                'label' => 'Teisingas',
                'data' => $answer->getCorrect(),
                'required'=>false
            ])
            ->add('save', SubmitType::class, array('label' => 'Įrašyti'))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formAnswer = $form->getData();

            $answer->setText($formAnswer->getText());
            $answer->setCorrect($formAnswer->getCorrect());
            $em = $this->getDoctrine()->getManager();

            $em->persist($answer);
            $em->flush();

            return $this->render('AppBundle:Admin:index.html.twig',[]);
        }

        return $this->render('AppBundle:Admin:create.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
