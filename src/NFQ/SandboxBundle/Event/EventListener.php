<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 14.15
 */

namespace NFQ\SandboxBundle\Event;

use NFQ\SandboxBundle\Service\Helicopter;

class EventListener
{
    /**
     * @param CreateEvent $event
     */
    public function makeChanges($event)
    {
        /** @var Helicopter $helicopter */
        $helicopter = $event->getHelicopter();
        $helicopter->setEngine('4.2 TFSI');
    }
}