<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.11
 * Time: 15.29
 */

namespace AppBundle\Event;
use Symfony\Component\EventDispatcher\Event;

class SendEvent extends Event
{
    private $UserReg;
    /**
     * @return mixed
     */
    public function getUserReg()
    {
        return $this->UserReg;
    }
    /**
     * @param mixed $UserReg
     * @return SendEvent
     */
    public function setUserReg($UserReg)
    {
        $this->UserReg = $UserReg;
        return $this;
    }
    /**
     * SendEvent constructor.
     * @param $UserReg
     */
    public function __construct($UserReg)
    {
        $this->UserReg = $UserReg;
    }
}
