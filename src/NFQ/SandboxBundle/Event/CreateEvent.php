<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 13.57
 */

namespace NFQ\SandboxBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class CreateEvent extends Event
{
 private $helicopter;

    /**
     * @return mixed
     */
    public function getHelicopter()
    {
        return $this->helicopter;
    }

    /**
     * @param mixed $helicopter
     * @return CreateEvent
     */
    public function setHelicopter($helicopter)
    {
        $this->helicopter = $helicopter;
        return $this;
    }

    /**
     * CreateEvent constructor.
     * @param $helicopter
     */
    public function __construct($helicopter)
    {
        $this->helicopter = $helicopter;
    }

}