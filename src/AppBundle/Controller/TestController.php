<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Answer;
use AppBundle\Entity\Question;
use AppBundle\Entity\Solution;
use AppBundle\Entity\Test;
use AppBundle\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/test/start/{id}" , name="test-start")
     */
    public function showTest($id, Request $request)
    {
        $hash = md5(microtime());

        $session = $request->getSession();
        $session->set('hash', $hash);

        $manager = $this->getDoctrine()->getManager();

        /** @var Test $test */
        $test = $manager->getRepository('AppBundle:Test')->find($id);
        $test->setQuestions($test->getQuestions());
        $test->setUser($test->getUser());

        $form = $this->createFormBuilder()
            ->add('start', SubmitType::class, array('label' => 'Pradėti testą', 'attr' => array('class' => 'btn btn-success btn-lg center-block')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()){
            /** @var QuestionRepository $question */
            $questionsId = $manager->getRepository('AppBundle:Question')->getRandomQuestions($test);
            $session->set('questionsId', $questionsId);

            return $this->redirectToRoute('test-solve', array('tid' => $id, 'qid' => 1,'hash' => $hash));
        }

        return $this->render('AppBundle:Test:start.html.twig', [
            'test' => $test,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/test/solve/{tid}/{qid}/{hash}", name="test-solve")
     */
    public function solveTest($tid, $qid, $hash, Request $request){
        /*
            TASKS:
            Skaiciuoti laika
        */

        $session = $request->getSession();
        $questionsId = $session->get('questionsId');

        if($hash!=$session->get('hash')){
            return $this->redirectToRoute('test-start', array('id' => $tid));
        }

        $manager = $this->getDoctrine()->getManager();

        /** @var Question $question */
        $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($questionsId[$qid-1]);

        /** @var Solution $old */
        $old = $manager->getRepository('AppBundle:Solution')->findOneBy(array('test' => $question->getTest(), 'question' => $question, 'hash' => $hash, 'user' => $this->getUser()));

        /** @var Answer $answers */
        $answers = $question->getAnswers();

        $choices = array();
        $selected = array();
        for($i=0;$i<count($answers);$i++){
            if($old!=null){
                foreach ($old->getAnswers() as $answer){
                    if(($answer==$answers[$i])&&(!in_array($answers[$i]->getId(), $selected))){
                        $selected[] = $answers[$i]->getId();
                    }
                }
            }
            $choices[] = array(
                $answers[$i]->getText()=>$answers[$i]->getId()
            );
        }

        $solution = new Solution();
        $solution->setQuestion($question);
        $solution->setHash($hash);
        $solution->setTest($question->getTest());
        $solution->setUser($this->getUser());

        $form = $this->createFormBuilder()
            ->add('answers', ChoiceType::class, array(
                'label' => $question->getText(),
                'choices' => $choices,
                'data' => $selected,
                'expanded' => true,
                'multiple' => true
            ))
            ->add('save', SubmitType::class, array('label' => 'Išsaugoti atsakymą', 'attr' => array('style' => 'float: left; margin-right: 10px')))
            ->add('end', SubmitType::class, array('label' => 'Baigti testą', 'attr' => array('onclick' => 'return confirm("Ar tikrai norite baigti testą?")')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $selections = $form->getData()["answers"];

            if($old!=null){
                $solution=$old;
                $solution->clearAnswers();
            }else{
                $manager->persist($solution);
            }

            foreach ($selections as $id){
                $solution->addAnswer($this->getDoctrine()->getRepository('AppBundle:Answer')->find($id));
            }

            if((count($solution->getAnswers())==0)&&($old!=null)){
                $solution->clearAnswers();
            }
            $manager->flush();

            if($form->get('save')->isClicked()){
                $this->get('session')->getFlashBag()->add(
                    'notice',
                    'Atsakymas išsaugotas'
                );
            }

            if($form->get('end')->isClicked()){
                $session->remove('hash');
                return $this->redirectToRoute('test-result', array('tid' => $tid, 'hash' => $hash));
            }
        }

        return $this->render('AppBundle:Test:solve.html.twig', array(
            'form' => $form->createView(),
            'testName' => $question->getTest()->getName(),
            'questionLimit' => $question->getTest()->getQuestionsLimit()
        ));
    }


    /**
     * @Route("/test/result/{tid}/{hash}", name="test-result")
     */
    public function showResult($tid, $hash){
        /** @var Solution $solution */
        $solutions = $this->getDoctrine()->getRepository('AppBundle:Solution')->findBy(array('test' => $tid, 'hash' => $hash));

        //var_dump($solution);

        if($solutions==null){
            $message = 'Toks testo sprendimas neegzistuoja';
            return $this->render('AppBundle:Test:error.html.twig', array(
                'message' => $message
            ));
        }else {
            if ($this->getUser() != $solutions[0]->getUser()) {
                $message = 'Peržiūrėti galima tik savo testų rezultatus';
                return $this->render('AppBundle:Test:error.html.twig', array(
                    'message' => $message
                ));
            }else{
                $test = $this->getDoctrine()->getRepository('AppBundle:Test')->find($tid);
                $maxPoints = $test->getQuestionsLimit();
                $points = 0;



                foreach ($solutions as $solution){
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
    public function showAnswers($tid, $qid, $hash, Request $request){
        /*
            TASKS:
            Parodyti teisingus atsakymus
         */
        $questions = $this->getDoctrine()->getRepository('AppBundle:Question')->findBy(array('test' => $tid));
        /** @var Solution $solution */
        $solution = $this->getDoctrine()->getRepository('AppBundle:Solution')->findOneBy(array('test' => $tid, 'question' => $questions[$qid-1], 'user' => $this->getUser(), 'hash' => $hash));

        if($solution==null){
            $message = 'Toks testo sprendimas neegzistuoja';
            return $this->render('AppBundle:Test:error.html.twig', array(
                'message' => $message
            ));
        }else {
            $answers = $solution->getQuestion()->getAnswers();

            $choices = array();
            $selected = array();
            for($i=0;$i<count($answers);$i++) {
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
            foreach ($answers as $answer){
                if($answer->getCorrect()==true){
                    $correctAnswers = $correctAnswers.$answer->getText()."\n";
                }
            }
            
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
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

    public function countPoints(Solution $solution){
        $points=0;
        $question = $this->getDoctrine()->getRepository('AppBundle:Question')->find($solution->getQuestion());
        $qAnswers = $question->getAnswers();
        $qCorrectAnswers = 0;
        foreach ($qAnswers as $answer){
            if($answer->getCorrect()==true){
                $qCorrectAnswers++;
            }
        }

        $answers = $solution->getAnswers();
        if(($answers!=null)&&(count($answers)>0)){
            $correctAnswers = 0;
            $incorrectAnswers = 0;
            foreach ($answers as $answer){
                if($answer->getCorrect()==true){
                    $correctAnswers++;
                }else{
                    $incorrectAnswers++;
                }
            }
            if($incorrectAnswers==0){
                if($qCorrectAnswers>1){
                    $points = $points + round(($correctAnswers/$qCorrectAnswers), 2);
                }else{
                    $points++;
                }
            }
        }
        return $points;
    }
}
