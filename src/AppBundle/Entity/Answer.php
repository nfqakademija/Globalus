<?php
/**
 * Created by PhpStorm.
 * User: lukas
 * Date: 16.11.13
 * Time: 22.30
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="answers")
 */
class Answer
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\Column(type="integer")
     * @ORM\ManyToOne(targetEntity="questions")
     */
    private $questionId;
    /**
     * @ORM\Column(length=500)
     */
    private $text;
    /**
     * @ORM\Column(type="boolean")
     */
    private $correct;

    /**
     * Answer constructor.
     * @param $id
     * @param $questionId
     * @param $text
     * @param $correct
     */
    public function __construct($id, $questionId, $text, $correct)
    {
        $this->id = $id;
        $this->questionId = $questionId;
        $this->text = $text;
        $this->correct = $correct;
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
     * @return Answer
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @param mixed $questionId
     * @return Answer
     */
    public function setQuestionId($questionId)
    {
        $this->questionId = $questionId;
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
     * @return Answer
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCorrect()
    {
        return $this->correct;
    }

    /**
     * @param mixed $correct
     * @return Answer
     */
    public function setCorrect($correct)
    {
        $this->correct = $correct;
        return $this;
    }

}