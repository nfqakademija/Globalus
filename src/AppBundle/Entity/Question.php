<?php
/**
 * Created by PhpStorm.
 * User: dziugas
 * Date: 16.11.9
 * Time: 17.27
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity()
 */

class Question
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $question;

    /**
     * @ORM\ManyToOne(targetEntity="Test", inversedBy="question")
     */
    protected $test;

    /**
     * @return mixed
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param mixed $test
     * @return Question
     */
    public function setTest($test)
    {
        $this->test = $test;
        return $this;
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



}