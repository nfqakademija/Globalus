<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="questions")
 */
class Question
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="tests")
     */
    private $testId;
    /**
     * @ORM\Column(length=255)
     */
    private $text;

    /**
     * Question constructor.
     * @param $id
     * @param $testId
     * @param $text
     */
    public function __construct($id, $testId, $text)
    {
        $this->id = $id;
        $this->testId = $testId;
        $this->text = $text;
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
     * @return Question
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTestId()
    {
        return $this->testId;
    }

    /**
     * @param mixed $testId
     * @return Question
     */
    public function setTestId($testId)
    {
        $this->testId = $testId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return Question
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

}