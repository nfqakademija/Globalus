<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;

/**
 * @ORM\Entity()
 * @ORM\Table(name="tests")
 */
class Test
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(length=255)
     */
    private $name;
    /**
     * @ORM\Column(type="text")
     */
    private $description;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timeLimit;
    /**
     * @ORM\Column(length=20, nullable=true)
     */
    private $password;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tests")
     */
    private $user;
    /**
     * @ORM\OneToMany(targetEntity="Question", mappedBy="test")
     */
    private $questions;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\Column(type="integer")
     */
    private $timesStarted;
    /**
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @return mixed
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return mixed
     */
    public function getTimesStarted()
    {
        return $this->timesStarted;
    }

    /**
     * @param mixed $timesStarted
     */
    public function setTimesStarted($timesStarted)
    {
        $this->timesStarted = $timesStarted;
    }



    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }


    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->published = false;
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
     * @return Test
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Test
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Test
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuestionsLimit()
    {
        return $this->questionsLimit;
    }

    /**
     * @param mixed $questionsLimit
     * @return Test
     */
    public function setQuestionsLimit($questionsLimit)
    {
        $this->questionsLimit = $questionsLimit;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getTimeLimit()
    {
        return $this->timeLimit;
    }

    /**
     * @param mixed $timeLimit
     * @return Test
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return Test
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
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
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param mixed $questions
     */
    public function setQuestions($questions)
    {
        $this->questions = $questions;
    }
    /**
     * @param Question $question
     */
    public function addQuestions($question)
    {
        $this->questions->add($question);
    }
}
