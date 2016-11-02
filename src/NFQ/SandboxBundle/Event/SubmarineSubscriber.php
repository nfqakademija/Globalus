<?php

namespace NFQ\SandboxBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubmarineSubscriber implements EventSubscriberInterface
{
    static public function getSubscribedEvents()
    {
        return array(
            Events::PRE_CREATE => "makeChanges",
        );
    }

    public function makeChanges(PreCreateEvent $event)
    {
        $submarine = $event->getSubmarine();
        $submarine->setSpeed('50km/h');
        $submarine->setLength('115m');
    }
}
