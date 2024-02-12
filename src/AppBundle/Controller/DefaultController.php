<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/login", name="login")
     */

    public function loginAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('login/login.html.twig');
    }
    /**
     * @Route("/products/{foto}", name="fotografias")
     */

    public function productsAction(Request $request,$foto)
    {
        // replace this example code with whatever you need
        return $this->render('fotografias/fotografias.html.twig',array("foto"=>$foto));
    }

}
