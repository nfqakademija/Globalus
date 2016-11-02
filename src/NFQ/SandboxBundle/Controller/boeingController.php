<?php

namespace NFQ\SandboxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class boeingController extends Controller
{
    /**
     * @Route("777")
     */
    public function indexAction()
    {

        $boeing = $this->container->get('app.boeing');


        return $this->render('NFQSandboxBundle:boeing:index.html.twig', array(
            "plane" => $boeing,
        ));
    }

}
