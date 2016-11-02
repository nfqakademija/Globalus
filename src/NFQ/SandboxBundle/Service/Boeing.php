<?php
/**
 * Created by PhpStorm.
 * User: dziugas
 * Date: 16.11.2
 * Time: 16.46
 */

namespace NFQ\SandboxBundle\Service;


class Boeing
{
    private $color;
    private $length;
    private $power;
    private $yearOfRelease;

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     * @return Boeing
     */
    public function setColor($color)
    {
        $this->color = $color;
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
     * @return Boeing
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @param mixed $power
     * @return Boeing
     */
    public function setPower($power)
    {
        $this->power = $power;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getYearOfRelease()
    {
        return $this->yearOfRelease;
    }

    /**
     * @param mixed $yearOfRelease
     * @return Boeing
     */
    public function setYearOfRelease($yearOfRelease)
    {
        $this->yearOfRelease = $yearOfRelease;
        return $this;
    }

    public function start()
    {
        return $this->color;
    }
}