<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Products;
use AppBundle\Entity\Categories;
use AppBundle\Entity\Users;
use AppBundle\Form\loginType;
use AppBundle\Form\registerType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


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
   
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        // Imprimir los datos para depuración
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
    /**
     * @Route("/register", name="register")
     */

    public function registerAction(Request $request)
    {
        $user = new Users();
        $form = $this->createForm(registerType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            if (strlen($user->getPlainPassword()) < 8 || !preg_match('/[A-Z]+/', $user->getPlainPassword()) || !preg_match('/[0-9]+/', $user->getPlainPassword())) {
                $this->addFlash('errorRegister', 'El password debe contener una letra mayuscula, almenos un numero y de almenos 8 caracteres');
                return $this->redirectToRoute('register');
            }
            if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('errorRegister', 'Debe ingresar un email');
                return $this->redirectToRoute('register');
            }
            $existingUser = $userRepository->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $this->addFlash('errorRegister', 'El email ya está registrado.');
                return $this->redirectToRoute('register');
            }
            $password =  $user->getPlainPassword();
            $user->setPassword($password);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('login/register.html.twig', array('form' => $form->createView()));
    }
}
