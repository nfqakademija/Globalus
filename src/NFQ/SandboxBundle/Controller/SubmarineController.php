<?php

namespace NFQ\SandboxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class SubmarineController extends Controller
{
    /**
     * @Route("/submarine")
     */
    public function indexAction()
    {
        $submarine = $this->container->get('app.submarine');

        return $this->render('NFQSandboxBundle:SubmarineController:index.html.twig', array(
            "sub" => $submarine,
        ));
    }

}
