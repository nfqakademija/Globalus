<?php
/**
 * Created by PhpStorm.
 * User: dziugas
 * Date: 16.11.2
 * Time: 18.09
 */

namespace NFQ\SandboxBundle\Event;


use Doctrine\Common\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Sub implements EventSubscriberInterface
{
    static public function getSubscribedEvents()
    {
        return array(
            Events::PRE_CREATE => "change",
        );
    }

    public function change(PreCreateEvent $event)
    {
        $boeing = $event->getBoeing();
        $boeing->setLength(61);
    }
}
