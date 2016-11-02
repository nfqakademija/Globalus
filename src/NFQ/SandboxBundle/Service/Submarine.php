<?php
/**
 * Created by PhpStorm.
 * User: lukas
 * Date: 16.11.2
 * Time: 16.46
 */

namespace NFQ\SandboxBundle\Service;


class Submarine
{
    private $cost;
    private $built;
    private $length;
    private $speed;
    private $type;

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     * @return Submarine
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBuilt()
    {
        return $this->built;
    }

    /**
     * @param mixed $built
     * @return Submarine
     */
    public function setBuilt($built)
    {
        $this->built = $built;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param mixed $length
     * @return Submarine
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * @param mixed $speed
     * @return Submarine
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Submarine
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function start()
    {
        return $this->cost;
    }
}