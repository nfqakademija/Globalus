<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 14.31
 */

namespace NFQ\SandboxBundle\Event;

use NFQ\SandboxBundle\Service\Helicopter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{

    static public function getSubscribedEvents()
    {
        return array(
            Events::CREATE_EVENT_SUB => 'changeEngine'
        );
        //return Events::CREATE_EVENT;
    }

    /**
     * @param CreateEvent $event
     */
    public function changeEngine($event){
        /** @var Helicopter $helicopter */
        $helicopter = $event->getHelicopter();
        $helicopter->setEngine('turbine engine');
        $helicopter->setFuel('jet fuel');
    }

}