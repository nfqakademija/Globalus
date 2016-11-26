<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.31
 * Time: 23.20
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Service\ExampleService;
use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    /**
     * @Route("/tests", name="user.tests")
     */
    public function showUserTests()
    {
        $testService = $this->get('app.tests');
        $tests=$testService->getUserTests($this->getUser()->getId());
        return $this->render('AppBundle:Profile:tests.html.twig',
            [
                'tests' => $tests
            ]
        );
    }
    /**
     * @Route("/tests/{id}", name="user.test")
     */
    public function showUserTest($id)
    {
        $testService = $this->get('app.tests');
        $test=$testService->getTestById($id);
        $questions=$testService->getTestQuestions($id);
        return $this->render('AppBundle:Profile:testInfo.html.twig',
            [
                'test' => $test,
                'questions' =>$questions
            ]
        );
    }
    /**
     * @Route("/tests/{id}/add/question", name="user.test.add.question")
     */
    public function addQuestionInTest($id,Request $request)
    {
        $testService = $this->get('app.tests');
        $test=$testService->getTestById($id);
        $question = new Question();

        $form = $this->createFormBuilder($question)
            ->add('text', TextType::class, [
                'label' => 'Klausimas'
            ])
            ->add('save', SubmitType::class, array('label' => 'Sukurti'))
            ->getForm();

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();
            $test->addQuestions($question);
            $question->setTest($test);
            $em->persist($question);
            $em->persist($test);
            $em->flush();

            return $this->render('AppBundle:Test:success.html.twig',[]);
        }

        return $this->render('AppBundle:Profile:createTest.html.twig',
            [
                'test' => $test,
                'form' => $form->createView(),
            ]
        );
    }
    /**
     * @Route("/questions/{id}/add/answer", name="user.question.add.answer")
     */
    public function addAnswerInQuestion($id,Request $request)
    {
        $testService = $this->get('app.tests');
        $question=$testService->getQuestionById($id);
        $answer = new Answer();

        $form = $this->createFormBuilder($answer)
            ->add('text', TextType::class, [
                'label' => 'Atsakymas'
            ])
            ->add('correct',CheckboxType::class,[
                'label' => 'Teisingas',
                'required'=>false
            ])
            ->add('save', SubmitType::class, array('label' => 'Sukurti'))
            ->getForm();

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $answer = $form->getData();
            $question->addAnswer($answer);
            $answer->setQuestion($question);
            $em->persist($question);
            $em->persist($answer);
            $em->flush();

            return $this->render('AppBundle:Test:success.html.twig',[]);
        }

        return $this->render('AppBundle:Profile:createTest.html.twig',
            [

                'form' => $form->createView(),
            ]
        );
    }
    /**
     * @Route("/tests/question/{id}", name="user.test.question")
     */
    public function showTestQuestion($id)
    {
        $testService = $this->get('app.tests');
        $questions=$testService->getQuestionById($id);
        $answers=$testService->getQuestionAnswers($id);
        return $this->render('AppBundle:Profile:questionInfo.html.twig',
            [
                'question' => $questions,
                'answers' => $answers
            ]
        );
    }


}
