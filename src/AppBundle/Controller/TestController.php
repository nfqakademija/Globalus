<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\Solution;
use AppBundle\Entity\Test;
use AppBundle\Form\TestStartType;
use AppBundle\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/create/tests" , name="createTest")
     */
    public function createTest(Request $request)
    {
        $test = new Test();

        $form = $this->createFormBuilder($test)
            ->add('name', TextType::class, [
                'label' => 'Pavadinimas'
            ])
            ->add('description', TextType::class, [
                'label' => 'Aprasymas'
            ])
            ->add('save', SubmitType::class, array('label' => 'Sukurti'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $test = $form->getData();
            $now=new \DateTime();
            $now->format('Y-m-d H:i:s');
            $test->setCreatedAt($now);
            $test->setUser($this->getUser());
            $test->setTimesStarted(0);
            $em = $this->getDoctrine()->getManager();

            $em->persist($test);
            $em->flush();

            return $this->render('AppBundle:Profile:index.html.twig', []);
        }

        return $this->render('AppBundle:Profile:createTest.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/test/start/{tid}" , name="test-start")
     */
    public function showTest($tid, Request $request)
    {
        $hash = md5(microtime());

        $cookies = $request->cookies;
        $session = $request->getSession();
        $session->set('hash', $hash);
        $manager = $this->getDoctrine()->getManager();

        /** @var Test $test */
        $test = $manager->getRepository('AppBundle:Test')->find($tid);
        $test->setTimesStarted($test->getTimesStarted()+1);
        $test->setQuestions($test->getQuestions());
        $test->setUser($test->getUser());

        $pass = false;
        if (strlen($test->getPassword())>0) {
            $pass = true;
        }

        if ($pass==false&&$session->has('error-message')) {
            $session->remove('error-message');
        }

        $form = $this->createForm(TestStartType::class, array('pass' => $pass));
        $form->handleRequest($request);

        if ($form->isSubmitted()&&$form->isValid()) {
            if ($pass==true) {
                $password = $form->getData()['password'];
                if ($password!=$test->getPassword()) {
                    $session->set('error-message', 'Neteisingas slaptažodis');
                    return $this->redirectToRoute('test-start', array('tid' => $tid));
                } else {
                    $session->remove('error-message');
                }
            }
            if ($cookies->has('deadline')) {
                $cookies->remove('deadline');
            }
            setcookie("deadline", $test->getTimeLimit(), time()+31536000, '/');
            /** @var QuestionRepository $question */
            $questionsId = $manager->getRepository('AppBundle:Question')->getRandomQuestions($test);

            foreach ($questionsId as $id) {
                $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($id);
                $solution = new Solution();
                $solution->setQuestion($question);
                $solution->setHash($hash);
                $solution->setTest($question->getTest());
                $solution->setUser($this->getUser());
                $manager->persist($solution);
                $manager->flush();
            }

            return $this->redirectToRoute('test-solve', array('tid' => $tid, 'qid' => 1,'hash' => $hash));
        }

        return $this->render('AppBundle:Test:start.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
            'error' => $session->get('error-message')
        ]);
    }

    /**
     * @Route("/test/stop/{tid}", name="test-stop")
     */
    public function forceStopTest($tid, Request $request)
    {
        $session = $request->getSession();
        $cookies = $request->cookies;
        $hash = $session->get('hash');
        if ($cookies->has('deadline')) {
            $cookies->remove('deadline');
        }
        return $this->redirectToRoute('test-result', array('tid' => $tid, 'hash' => $hash));
    }

    /**
     * @Route("/test/solve/{tid}/{qid}/{hash}", name="test-solve")
     */
    public function solveTest($tid, $qid, $hash, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $cookies = $request->cookies;
        if ($cookies->has('deadline')) {
            $deadline = $cookies->get('deadline');
            $cookies->remove('deadline');
        }

        $criteria = array('test' => $tid, 'hash' => $hash, 'user' => $this->getUser());
        /** @var Solution $solution */
        $solution = $manager->getRepository('AppBundle:Solution')->findBy($criteria)[$qid-1];

        if ($hash!=$session->get('hash')) {
            return $this->redirectToRoute('test-start', array('id' => $tid));
        }

        /** @var Question $sQuestion */
        $sQuestion = $solution->getQuestion();

        /** @var Question $question */
        $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($sQuestion->getId());

        /** @var Answer $sAnswers */
        $sAnswers = $solution->getAnswers();

        /** @var Answer $answers */
        $answers = $question->getAnswers();

        $choices = array();
        $selected = array();
        for ($i=0; $i<count($answers); $i++) {
            if ($sAnswers!=null) {
                foreach ($sAnswers as $answer) {
                    if (($answer==$answers[$i])&&(!in_array($answers[$i]->getId(), $selected))) {
                        $selected[] = $answers[$i]->getId();
                    }
                }
            }
            $choices[] = array(
                $answers[$i]->getText()=>$answers[$i]->getId()
            );
        }

        $form = $this->createFormBuilder()
            ->add('answers', ChoiceType::class, array(
                'label' => $question->getText(),
                'choices' => $choices,
                'data' => $selected,
                'expanded' => true,
                'multiple' => true
            ))
            ->add('save', SubmitType::class, array('label' => 'Išsaugoti atsakymą',
                'attr' => array('style' => 'float: left; margin-right: 10px')))
            ->add('end', SubmitType::class, array('label' => 'Baigti testą',
                'attr' => array('onclick' => 'return confirm("Ar tikrai norite baigti testą?")')))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selections = $form->getData()["answers"];
            $solution->clearAnswers();

            if (count($selections)>0) {
                foreach ($selections as $id) {
                    $solution->addAnswer($this->getDoctrine()->getRepository('AppBundle:Answer')->find($id));
                }
            }

            $manager->flush();

            if ($form->get('save')->isClicked()) {
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Atsakymas išsaugotas'
                );
            }

            if ($form->get('end')->isClicked()) {
                $session->remove('hash');
                $cookies = $request->cookies;
                if ($cookies->has('deadline')) {
                    $cookies->remove('deadline');
                }
                return $this->redirectToRoute('test-result', array('tid' => $tid, 'hash' => $hash));
            }
        }

        return $this->render('AppBundle:Test:solve.html.twig', array(
            'form' => $form->createView(),
            'testName' => $question->getTest()->getName(),
            'questionLimit' => $question->getTest()->getQuestionsLimit(),
            'deadline' => $deadline
        ));
    }


    /**
     * @Route("/test/result/{tid}/{hash}", name="test-result")
     */
    public function showResult($tid, $hash, Request $request)
    {
        $session = $request->getSession();
        $sessionHash = $session->get('hash');
        if ($sessionHash!=null) {
            $session->remove('hash');
        }

        /** @var Solution $solution */
        $solutions = $this->getDoctrine()->getRepository('AppBundle:Solution')
            ->findBy(array('test' => $tid, 'hash' => $hash));

        if ($solutions==null) {
            $message = 'Toks testo sprendimas neegzistuoja';
            return $this->render('AppBundle:Test:error.html.twig', array(
                'message' => $message
            ));
        } else {
            if ($this->getUser() != $solutions[0]->getUser()) {
                $message = 'Peržiūrėti galima tik savo testų rezultatus';
                return $this->render('AppBundle:Test:error.html.twig', array(
                    'message' => $message
                ));
            } else {
                $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($tid);
                $maxPoints = $test->getQuestionsLimit();
                $points = 0;

                foreach ($solutions as $solution) {
                    $points += $this->countPoints($solution);
                }

                $result = round(($points*100/$maxPoints), 2);

                return $this->render('AppBundle:Test:result.html.twig', array(
                    'testName' => $test->getName(),
                    'result' => $result
                ));
            }
        }
    }

    /**
     * @Route("/test/answers/{tid}/{qid}/{hash}", name="test-answers")
     */
    public function showAnswers($tid, $qid, $hash, Request $request)
    {
        /** @var Solution $solution */
        $solution = $this->getDoctrine()->getRepository('AppBundle:Solution')
            ->findBy(array('test' => $tid, 'hash' => $hash))[$qid-1];

        if ($solution==null) {
            $message = 'Toks testo sprendimas neegzistuoja';
            return $this->render('AppBundle:Test:error.html.twig', array(
                'message' => $message
            ));
        } else {
            $answers = $solution->getQuestion()->getAnswers();

            $choices = array();
            $selected = array();
            for ($i=0; $i<count($answers); $i++) {
                foreach ($solution->getAnswers() as $answer) {
                    if (($answer == $answers[$i]) && (!in_array($answers[$i]->getId(), $selected))) {
                        $selected[] = $answers[$i]->getId();
                    }
                }
                $choices[$answers[$i]->getText()] = $answers[$i]->getId();
            }

            $form = $this->createFormBuilder()
                ->add('answers', ChoiceType::class, array(
                    'label' => $solution->getQuestion()->getText(),
                    'choices' => $choices,
                    'data' => $selected,
                    'expanded' => true,
                    'multiple' => true,
                    'disabled' => true
                ))
                ->add('save', SubmitType::class, array('label' => 'Baigti peržiūrą'))
                ->getForm();

            $correctAnswers = "";
            foreach ($answers as $answer) {
                if ($answer->getCorrect()==true) {
                    $correctAnswers = $correctAnswers.$answer->getText()."\n";
                }
            }

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                return $this->redirectToRoute('test-result', array('tid' => $tid, 'hash' => $hash));
            }

            $points = $this->countPoints($solution);

            return $this->render('AppBundle:Test:answers.html.twig', array(
                'form' => $form->createView(),
                'testName' => $solution->getTest()->getName(),
                'questionLimit' => $solution->getTest()->getQuestionsLimit(),
                'correctAnswers' => $correctAnswers,
                'points' => $points
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
