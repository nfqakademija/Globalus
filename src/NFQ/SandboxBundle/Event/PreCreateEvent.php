<?php

namespace NFQ\SandboxBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class PreCreateEvent extends Event
{
    const NAME = 'app.pre_create';

    private $submarine;

    public function __construct($submarine)
    {
        $this->setSubmarine($submarine);
    }
    /**
     * @return mixed
     */
    public function getSubmarine()
    {
        return $this->submarine;
    }
    /**
     * @param mixed $submarine
     */
    public function setSubmarine($submarine)
    {
        $this->submarine = $submarine;
    }
}