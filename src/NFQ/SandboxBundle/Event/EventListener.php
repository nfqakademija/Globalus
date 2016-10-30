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
        $helicopter->setEngine('piston engine');
        $helicopter->setRotorSpeed('500rpm');
        $helicopter->setColor('green');
        $helicopter->setFuel('diesel');
    }
}