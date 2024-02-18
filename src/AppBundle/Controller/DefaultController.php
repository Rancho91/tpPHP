<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Products;
use AppBundle\Entity\Users;
use AppBundle\Form\categoriesFilterType;
use AppBundle\Form\registerType;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $product = new Products();

        $form = $this->createForm(categoriesFilterType::class );
        $form->handleRequest($request);
        $sql = 'SELECT p.id id, p.name producto, p.image, p.deleted deleted, c.name categoria FROM products p JOIN categories c ON c.id = p.category_id WHERE p.deleted = 0';
        var_dump($form->isSubmitted());
        var_dump($form->isValid());

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $category = $data['categories'];

            if ($category !== null) {
                $sql .= ' AND c.id = ' . $category->getId();
            }
        }
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();
        $products = $statement->fetchAll();
        return $this->render('default/index.html.twig', array('products' => $products, 'form' => $form->createView()));
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
        var_dump($form->isSubmitted());
        var_dump($form->isValid());
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
