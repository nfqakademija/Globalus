<?php
/**
 * Created by PhpStorm.
 * User: dziugas
 * Date: 16.11.9
 * Time: 17.27
 */

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Test
 *
 * @ORM\Table(name="test")
 * @ORM\Entity()
 */

class Test
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Question", mappedBy="test")
     */
    protected $questions;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $answer;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
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
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     * @return Test
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }

    /**
     * @param Question $question
     * @return $this
     */
    public function addQuestion($question)
    {
        $this->questions->add($question);
        $question->setTest($this);
        return $this;
    }

    public function getQuestions()
    {
        return $this->questions;
    }


}