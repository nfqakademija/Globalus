<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.11.11
 * Time: 15.14
 */

namespace AppBundle\Event;
use AppBundle\Controller\RegistrationController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
class EventSubscriber implements EventSubscriberInterface
{
    protected $container;

    public function __construct(ContainerInterface $container) // this is @service_container
    {
        $this->container = $container;
    }
    static public function getSubscribedEvents()
    {
        return array(
            Events::CREATE_EVENT => 'sendRegistrationConfirm',
            Events::RESET_EMAIL_EVENT => 'sendReset'
        );
        //return Events::CREATE_EVENT;
    }
    /**
     * @param SendEvent $event
     */
    public function sendRegistrationConfirm($event){
        $user = $event->getUserReg()->getUser();

        $confirmationToken = $event->getUserReg()->getComfirmationToken();
        $message = \Swift_Message::newInstance()
            ->setSubject('Pabaikite registraciją')
            ->setFrom('nfqglobalus@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->container->get('templating')->render(
                    'AppBundle:Email:registration.html.twig',
                    array('name' => $user->getUsername(),
                        'token' => $confirmationToken)
                ),
                'text/html'
            );
        echo $message->getBody();

        $this->container->get('swiftmailer.mailer.default')->send($message);
    }

    /**
     * @param SendEvent $event
     */
    public function sendReset($event){
        $user = $event->getUserReg()->getUser();
        $confirmationToken = $event->getUserReg()->getComfirmationToken();
        $message = \Swift_Message::newInstance()
            ->setSubject('Slaptažodžio atstatymas')
            ->setFrom('nfqglobalus@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->container->get('templating')->render(
                    'AppBundle:Email:reset.html.twig',
                    array('name' => $user->getUsername(),
                        'token' => $confirmationToken)
                ),
                'text/html'
            );
        $this->container->get('swiftmailer.mailer.default')->send($message);
    }
}
