<?php
/**
 * Created by PhpStorm.
 * User: dziugas
 * Date: 16.11.2
 * Time: 17.20
 */

namespace NFQ\SandboxBundle\Event;


class EventListener
{
    public function makeChanges($event)
    {

        $boeing = $event->getBoeing();
        $boeing->setColor('white');
    }
}