<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.11
 * Time: 14.58
 */

namespace AppBundle\Event;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Entity\User;
use AppBundle\Event\UserReg;

class EmailSender
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param User $user
     * @param $confirmation_token
     */
    public function send($user,$confirmation_token)
    {

        $userReg = new UserReg($user,$confirmation_token);
        $this->eventDispatcher->dispatch(Events::CREATE_EVENT,new SendEvent($userReg) );

    }

}