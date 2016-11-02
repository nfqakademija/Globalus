<?php
/**
 * Created by PhpStorm.
 * User: lukas
 * Date: 16.11.2
 * Time: 17.01
 */

namespace NFQ\SandboxBundle\Service;

use NFQ\SandboxBundle\Service\Submarine;
use NFQ\SandboxBundle\Event\Events;
use NFQ\SandboxBundle\Event\PreCreateEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SubmarineFactory
{
    /**
     * @var EventDispatcher
     */

    private $eventDispatcher;

    public function __construct($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function create()
    {
        $submarine = new Submarine();
        $submarine->setCost('2.688bil');
        $submarine->setBuilt(2000);
        $submarine->setType('Attack submarine');
        $this->eventDispatcher->dispatch(Events::PRE_CREATE, new PreCreateEvent($submarine));
        return $submarine;
    }
}