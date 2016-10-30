<?php
/**
 * Created by PhpStorm.
 * User: arnoldas
 * Date: 16.10.30
 * Time: 15.06
 */

namespace NFQ\SandboxBundle\Controller;
use NFQ\SandboxBundle\Service\SandboxService;
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


        //$SandboxService = $this->get('app.sandbox');
        //$helis = $SandboxService->getHelicopterEngines();
        return $this->render('::sandboxRezults.html.twig', [
            'helis' => $helis,
            ]);
    }

    /**
     * @Route("/list", name="posts_list")
     */
    /*public function listAction()
    {
        $output->writeln('Set and get');
        $helicopter =$this->getContainer()->get('app.helicopter_set_get');
        $output->writeln($helicopter->getEngine());
        $output->writeln('Event listener');
        $helicopter=$this->getContainer()->get('app.helicopter');
        $output->writeln($helicopter->getEngine());
        $output->writeln('Event subscriber');
        $helicopter=$this->getContainer()->get('app.helicopter_sub');
        $output->writeln($helicopter->getEngine());
        return $this->render('SandboxBundle:Home:index.html.twig', [
            'posts' => $posts,
        ]);
    }*/
}