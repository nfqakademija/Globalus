<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 12.03
 */

namespace NFQ\SandboxBundle\Service;
use NFQ\SandboxBundle\Event\Events;
use NFQ\SandboxBundle\Event\CreateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class HelicopterFactory
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * HelicopterFactory constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    public function createAndSetEngine(){
        $helicopter = new Helicopter();
        $helicopter->setEngine('v8');
        return $helicopter;
    }
    public function create(){
        $helicopter = new Helicopter();
        $helicopter->setEngine('v8');
        $this->eventDispatcher->dispatch(Events::CREATE_EVENT,new CreateEvent($helicopter));
        return $helicopter;
    }
    public function createSub(){
        $helicopter = new Helicopter();
        $helicopter->setEngine('v8');
        $this->eventDispatcher->dispatch(Events::CREATE_EVENT_SUB,new CreateEvent($helicopter));
        return $helicopter;
    }

}