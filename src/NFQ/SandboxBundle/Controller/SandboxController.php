<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 15.06
 */

namespace NFQ\SandboxBundle\Controller;
use NFQ\SandboxBundle\Service\SandboxService;
use NFQ\SandboxBundle\Service\Helicopter;
use NFQ\SandboxBundle\Service\HelicoperChild;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use NFQ\SandboxBundle\Command\AppDebugCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
class SandboxController extends Controller
{
    /**
     * @Route("/", name="Sandbox homepage")
     */
    public function indexAction()
    {

        $helis = [
                 [
                     'title' => 'Set and Get',
                     'engine' => $this->container->get('app.helicopter_set_get')->getEngine()
                 ],
                 [
                     'title' => 'Event Listener',
                     'engine' => $this->container->get('app.helicopter')->getEngine()
                 ],
                 [
                     'title' => 'Event Subscriber',
                    'engine' => $this->container->get('app.helicopter_sub')->getEngine()
                 ]
             ];


        return $this->render('::sandboxRezults.html.twig', [
            'helis' => $helis,
            ]);
    }
}