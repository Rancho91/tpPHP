<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Products;
use AppBundle\Entity\Categories;
use AppBundle\Entity\Users;
use AppBundle\Form\loginType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Products::class);
        $products =  $repository->findAll();
        // var_dump($products);
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array('products' => $products));
    }



    /**
     * @Route("/login", name="login")
     */

    public function loginAction(Request $request)
    {
        $user = new Users();
        $form = $this->createForm(loginType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $formData = $form->getData();
            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            $user = $userRepository->findOneBy(['email' => $formData['email']]);
            return $this->redirectToRoute('dashboard');
            // } else {
            //     $this->addFlash('error', 'Usuario o contraseña incorrectos.');
            // }
        }

        return $this->render('login/login.html.twig', array('form'=>$form->createView()));
    }
}
