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
    private $color;
    private $fuel;
    private $rotor_speed;
    /**
     * Used Singleton pattern
     * It is used to access one and only one instance of a particular class(this time Helicopter)
     * @var $helicopter is the reference to *Singleton* instance of this class
     */
    private static $helicopter;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return $helicopter The *Singleton* instance.
     */
    public static function getHelicopter()
    {
        if (null === static::$helicopter) {
            static::$helicopter = new static();
        }

        return static::$helicopter;
    }
    /**
     *  The constructor is private
     *  to prevent initiation with outer code.
     *
     */
    private function __construct()
    {
    }
    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }



    /**
     * @return mixed
     */
    public function getRotorSpeed()
    {
        return $this->rotor_speed;
    }

    /**
     * @param mixed $rotor_speed
     * @return Helicopter
     */
    public function setRotorSpeed($rotor_speed)
    {
        $this->rotor_speed = $rotor_speed;
        return $this;
    }



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
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     * @return Helicopter
     */
    public function setColor($color)
    {
        $this->color = $color;
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

    public function toString(){
        return $this->getEngine()." | ".$this->getColor()." | ".$this->getFuel()." | " .$this->getRotorSpeed();
    }



}