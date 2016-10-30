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
    public function create($bool){
        $helicopter = Helicopter::getHelicopter();
        $helicopter->setEngine('v8');
        $helicopter->setRotorSpeed('450rpm');
        $helicopter->setColor('blue');
        $helicopter->setFuel('aviation fuel');
        switch ($bool){
            case 1: break;
            case 2: {
                $this->eventDispatcher->dispatch(Events::CREATE_EVENT,new CreateEvent($helicopter));
                break;
            }
            case 3:{
                $this->eventDispatcher->dispatch(Events::CREATE_EVENT_SUB,new CreateEvent($helicopter));
                break;
            }
        }
        return $helicopter;
    }


}