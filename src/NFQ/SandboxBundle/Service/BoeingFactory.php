<?php
/**
 * Created by PhpStorm.
 * User: dziugas
 * Date: 16.11.2
 * Time: 17.01
 */

namespace NFQ\SandboxBundle\Service;

use NFQ\SandboxBundle\Event\Events;
use NFQ\SandboxBundle\Event\PreCreateEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BoeingFactory
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
        $boeing = new Boeing();
        $boeing->setColor('red');
        $boeing->setLength(5);
        $boeing->setPower(111000);
        $boeing->setYearOfRelease(1994);
        $this->eventDispatcher->dispatch(Events::PRE_CREATE, new PreCreateEvent($boeing));
        return $boeing;
    }
}