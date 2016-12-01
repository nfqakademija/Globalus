<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;

/**
 * @ORM\Entity()
 * @ORM\Table(name="solutions")
 */
class Solution
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="Test", inversedBy="solutions")
     */
    private $test;
    /**
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="solutions")
     */
    private $question;
    /**
     * @ORM\ManyToMany(targetEntity="Answer", inversedBy="solutions")
     */
    private $answers;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="solutions")
     */
    private $user;
    /**
     * @ORM\Column(length=255)
     */
    private $hash;


    /**
     * Solution constructor.
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * @param mixed $questions
     */
    public function setQuestion($questions)
    {
        $this->question = $questions;
    }

    public function clearAnswers()
    {
        $this->getAnswers()->clear();
    }

    public function addAnswer($answer)
    {
        $this->answers->add($answer);

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param mixed $test
     */
    public function setTest($test)
    {
        $this->test = $test;
    }

    /**
     * @return mixed
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param mixed $answers
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }




}