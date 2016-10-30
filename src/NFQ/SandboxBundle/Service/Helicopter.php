<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 11.47
 */

namespace NFQ\SandboxBundle\Service;


class Helicopter
{
    private $engine;
    private $seats;
    private $control_stick;
    private $fuel;
    private $skid;
    private $rotors;

    /**
     * @return mixed
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param mixed $engine
     * @return Helicopter
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSeats()
    {
        return $this->seats;
    }

    /**
     * @param mixed $seats
     * @return Helicopter
     */
    public function setSeats($seats)
    {
        $this->seats = $seats;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getControlStick()
    {
        return $this->control_stick;
    }

    /**
     * @param mixed $control_stick
     * @return Helicopter
     */
    public function setControlStick($control_stick)
    {
        $this->control_stick = $control_stick;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFuel()
    {
        return $this->fuel;
    }

    /**
     * @param mixed $fuel
     * @return Helicopter
     */
    public function setFuel($fuel)
    {
        $this->fuel = $fuel;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSkid()
    {
        return $this->skid;
    }

    /**
     * @param mixed $skid
     * @return Helicopter
     */
    public function setSkid($skid)
    {
        $this->skid = $skid;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRotors()
    {
        return $this->rotors;
    }

    /**
     * @param mixed $rotors
     * @return Helicopter
     */
    public function setRotors($rotors)
    {
        $this->rotors = $rotors;
        return $this;
    }


}