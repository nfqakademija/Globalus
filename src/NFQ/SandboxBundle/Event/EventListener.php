<?php

namespace NFQ\SandboxBundle\Event;

use NFQ\SandboxBundle\Service\Submarine;

class EventListener
{
    /**
     * @param PreCreateEvent $event
     */
    public function makeChanges($event)
    {
        /** @var Submarine $submarine */

        $submarine = $event->getSubmarine();
        $submarine->setCost('4.2 bil');
    }
}