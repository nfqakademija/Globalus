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
                     'data' => $this->container->get('app.helicopter_set_get')->toString()
                 ],
                 [
                     'title' => 'Event Listener',
                     'data' => $this->container->get('app.helicopter')->toString()
                 ],
                 [
                     'title' => 'Event Subscriber',
                    'data' => $this->container->get('app.helicopter_sub')->toString()
                 ]
             ];


        return $this->render('::sandboxRezults.html.twig', [
            'helis' => $helis,
            ]);
    }
}