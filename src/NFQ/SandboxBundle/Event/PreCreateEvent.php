<?php
/**
 * Created by PhpStorm.
 * User: dziugas
 * Date: 16.11.2
 * Time: 17.18
 */

namespace NFQ\SandboxBundle\Event;


use Symfony\Component\EventDispatcher\Event;

class PreCreateEvent extends Event
{


    const NAME = 'app.pre_create';
    private $boeing;

    public function __construct($boeing)
    {
        $this->boeing = $boeing;
    }

    /**
     * @return mixed
     */
    public function getBoeing()
    {
        return $this->boeing;
    }

    /**
     * @param mixed $boeing
     * @return PreCreateEvent
     */
    public function setBoeing($boeing)
    {
        $this->boeing = $boeing;
        return $this;
    }
}