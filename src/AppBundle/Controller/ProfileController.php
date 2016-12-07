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
use AppBundle\Form\AnswerType;
use AppBundle\Form\QuestionType;
use AppBundle\Form\TestType;
use AppBundle\Entity\Solution;
use AppBundle\Service\ExampleService;
use FOS\UserBundle\Form\Type\ChangePasswordFormType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Test;
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



            return $this->redirectToRoute('app.successPassChange', ['id' => $user->getId()]);
        }

        return $this->render('AppBundle:Profile:create.html.twig', [
                'form' => $form->createView()
            ]);
    }
    /**
     * @Route("/successPassChange/{id}", name="app.successPassChange")
     */
    public function showSuccessPassChange($id)
    {
        $userService = $this->get('app.user');
        return $this->render('AppBundle:Profile:successPassChange.html.twig', [
                'user' => $userService->getUserById($id),
            ]);
    }
    /**
     * @Route("/tests/{page}", name="user.tests")
     */
    public function showUserTests($page = 1)
    {
        $testService = $this->get('app.tests');
        $limit = 10;

        $tests=$testService->getUserTests($this->getUser()->getId(), $page, $limit);
        $maxPages = ceil($tests->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Profile:tests.html.twig', [
                'tests' => $tests,
                'maxPages' => $maxPages,
                'thisPage' => $thisPage
            ]);
    }

    /**
     * @Route("/tests/test/delete/{id}", name="user.tests.delete")
     */
    public function deleteUserTest($id)
    {
        $testService = $this->get('app.tests');
        $test = $testService->getTestById($id);
        $questions = $test->getQuestions();
        $em = $this->getDoctrine()->getManager();
        foreach ($questions as $question) {
            $answers = $question->getAnswers();
            foreach ($answers as $answer) {
                $em->remove($answer);
            }
            $em->remove($question);
        }
        $em->remove($test);
        $em->flush();
        return $this->redirectToRoute('user.tests');
    }

    /**
     * @Route("/tests/test/question/delete/{id}", name="user.question.delete")
     */
    public function deleteTestQuestion($id)
    {
        $testService = $this->get('app.tests');
        $question = $testService->getQuestionById($id);
        $em = $this->getDoctrine()->getManager();
        $answers = $question->getAnswers();
        foreach ($answers as $answer) {
            $em->remove($answer);
        }
        $em->remove($question);
        $em->flush();
        return $this->redirectToRoute('user.test', array('id' => $question->getTest()->getId()));
    }

    /**
     * @Route("/tests/test/question/answer/delete/{id}", name="user.answer.delete")
     */
    public function deleteQuestionAnswer($id)
    {
        $testService = $this->get('app.tests');
        $answer = $testService->getAnswerById($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($answer);
        $em->flush();
        return $this->redirectToRoute('user.test.question', array('id' => $answer->getQuestion()->getId()));
    }
    /**
     * @Route("/tests/test/{id}/{page}", name="user.test")
     */
    public function showUserTest($id, $page = 1)
    {
        $testService = $this->get('app.tests');
        $test=$testService->getTestById($id);
        $limit = 10;
        $questions=$testService->getTestQuestions($id, $page, $limit);
        $maxPages = ceil($questions->count() / $limit);
        $thisPage = $page;
        return $this->render('AppBundle:Profile:testInfo.html.twig', [
                'test' => $test,
                'questions' =>$questions,
                'maxPages' => $maxPages,
                'thisPage' => $thisPage
            ]);
    }
    /**
     * @Route("/tests/edit/{id}", name="edit_user_test")
     */
    public function editUserTest($id, Request $request)
    {
        $testService = $this->get('app.tests');
        $test=$testService->getTestById($id);

        $form = $this->createForm(TestType::class, $test);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $test = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($test);
            $em->flush();
            return $this->redirectToRoute('user.test', array('id' => $test->getId()));
        }

        return $this->render('AppBundle:Profile:createTest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/questions/edit/{id}", name="edit_test_question")
     */
    public function editTestQuestion($id, Request $request)
    {
        $testService = $this->get('app.tests');
        $question=$testService->getQuestionById($id);

        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($question);
            $em->flush();
            return $this->redirectToRoute('user.test.question', array('id' => $question->getId()));
        }
        return $this->render('AppBundle:Profile:createTest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/answers/edit/{id}", name="edit_question_answer")
     */
    public function editQuestionAnswer($id, Request $request)
    {
        $testService = $this->get('app.tests');
        $answer=$testService->getAnswerById($id);

        $form = $this->createForm(AnswerType::class, $answer);
        $form->add('save', SubmitType::class, array('label' => 'Sukurti'));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($answer);
            $em->flush();
            return $this->redirectToRoute('user.test.question', array('id' => $answer->getQuestion()->getId()));
        }

        return $this->render('AppBundle:Profile:createTest.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/tests/{id}/add/question", name="user.test.add.question")
     */
    public function addQuestionInTest($id, Request $request)
    {
        $testService = $this->get('app.tests');
        $test=$testService->getTestById($id);
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->getData();
            $test->addQuestions($question);
            $question->setTest($test);
            $em->persist($question);
            $em->persist($test);
            $em->flush();
            return $this->redirectToRoute('user.test', array('id' => $test->getId()));
        }

        return $this->render('AppBundle:Profile:createTest.html.twig', [
                'test' => $test,
                'form' => $form->createView(),
            ]);
    }
    /**
     * @Route("/questions/{id}/add/answer", name="user.question.add.answer")
     */
    public function addAnswerInQuestion($id, Request $request)
    {
        $testService = $this->get('app.tests');
        $question = $testService->getQuestionById($id);
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->add('save', SubmitType::class, array('label' => 'Sukurti'));

        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $answer = $form->getData();
            $question->addAnswer($answer);
            $answer->setQuestion($question);
            $em->persist($question);
            $em->persist($answer);
            $em->flush();

            return $this->redirectToRoute('user.test.question', array('id' => $id));
        }

        return $this->render('AppBundle:Profile:createTest.html.twig', [

                'form' => $form->createView(),
            ]);
    }
    /**
     * @Route("/tests/question/{id}/{page}", name="user.test.question")
     */
    public function showTestQuestion($id, $page = 1)
    {
        $testService = $this->get('app.tests');
        $question = $testService->getQuestionById($id);
        $limit = 5;
        $answers = $testService->getQuestionAnswers($id, $page, $limit);
        $maxPages = ceil($answers->count() / $limit);
        $thisPage = $page;

        return $this->render('AppBundle:Profile:questionInfo.html.twig', [
            'question' => $question,
            'answers' => $answers,
            'maxPages' => $maxPages,
            'thisPage' => $thisPage,
            'test' => $question->getTest()
        ]);
    }

    /**
     * @Route("/test-history", name="profile-test-results")
     */
    public function showTestHistory()
    {
        /** @var Solution $solution */
        $solutions = $this->getDoctrine()->getRepository('AppBundle:Solution')
            ->findBy(array('user' => $this->getUser()));

        $tests = array();
        $temp = array();

        if ($solutions!=null) {
            $hash = $solutions[0]->getHash();
            for ($i = 0; $i < count($solutions)-1; $i++) {
                if ($solutions[$i]->getHash() == $hash) {
                    $temp[] = $solutions[$i];
                    if ($solutions[$i+1]->getHash()!=$hash) {
                        $tests[] = $temp;
                        $hash = $solutions[$i+1]->getHash();
                        unset($temp);
                        $temp = array();
                    }
                    if ($i+1==count($solutions)-1) {
                        $temp[] = $solutions[$i+1];
                        $tests[] = $temp;
                    }
                }
            }
        }

        if ($solutions==null) {
            $message = 'Testų istorija tuščia';
            return $this->render('AppBundle:Profile:testHistory.html.twig', array(
                'message' => $message
            ));
        } else {
            $results = array();
            $testObjects = array();
            $hashes = array();
            foreach ($tests as $test) {
                $maxPoints = $test[0]->getTest()->getQuestionsLimit();
                $points = 0;
                foreach ($test as $solution) {
                    $points += $this->countPoints($solution);
                }
                $hashes[] = $test[0]->getHash();
                $results[] = round(($points*100/$maxPoints), 2);
                $testObjects[] = $this->getDoctrine()->getRepository('AppBundle:Test')->find($test[0]->getTest());
            }

            return $this->render('AppBundle:Profile:testHistory.html.twig', array(
                'results' => $results,
                'tests' => $testObjects,
                'hashes' => $hashes,
                'message' => ''
            ));
        }
    }

    public function countPoints(Solution $solution)
    {
        $points=0;
        $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($solution->getQuestion());
        $qAnswers = $question->getAnswers();
        $qCorrectAnswers = 0;
        foreach ($qAnswers as $answer) {
            if ($answer->getCorrect()==true) {
                $qCorrectAnswers++;
            }
        }

        $answers = $solution->getAnswers();
        if (($answers!=null)&&(count($answers)>0)) {
            $correctAnswers = 0;
            $incorrectAnswers = 0;
            foreach ($answers as $answer) {
                if ($answer->getCorrect()==true) {
                    $correctAnswers++;
                } else {
                    $incorrectAnswers++;
                }
            }
            if ($incorrectAnswers==0) {
                if ($qCorrectAnswers>1) {
                    $points = $points + round(($correctAnswers/$qCorrectAnswers), 2);
                } else {
                    $points++;
                }
            }
        }
        return $points;
    }
}
