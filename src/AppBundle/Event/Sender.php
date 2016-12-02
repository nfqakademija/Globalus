<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.12
 * Time: 17.59
 */

namespace AppBundle\Event;

abstract class Sender
{
    abstract protected function send($user, $confirmation_token, $action);
}
