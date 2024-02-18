<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Categories;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Products;
use AppBundle\Entity\Users;
use AppBundle\Form\categoriesFilterType;
use AppBundle\Form\registerType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Validator\Constraints\Valid;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(categoriesFilterType::class);
        $form->handleRequest($request);
        $sql = 'SELECT p.id id, p.name producto, p.image, p.deleted deleted, c.name categoria FROM products p JOIN categories c ON c.id = p.category_id WHERE p.deleted = 0';
        $repository = $this->getDoctrine()->getRepository(Categories::class);
        $categories =  $repository->findAll();
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
        return $this->render('default/index.html.twig', array('products' => $products, 'form' => $form->createView(), 'categories' => $categories));
    }
    /**
     * @Route("/filterproduct/{id}", name="filterProduct")
     */
    public function filterProductAction(Request $request, $id = 0)
    {
        $sql = 'SELECT p.id id, p.name producto, p.image, p.deleted deleted, c.name categoria FROM products p JOIN categories c ON c.id = p.category_id WHERE p.deleted = 0';
        $repository = $this->getDoctrine()->getRepository(Categories::class);
        $categories =  $repository->findAll();
        if ($id > 0) {
            $sql .= ' AND c.id = ' . $id;
        }
        $em = $this->getDoctrine()->getManager();
        $connection = $em->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute();
        $products = $statement->fetchAll();
        return $this->json([
            'success' => true,
            'state' => $products
        ]);
    }
    /**
     * @Route("/login", name="login")
     */

    public function loginAction(Request $request)
    {
        $error = null;
        $lastUsername = '';

        if ($request->isMethod('POST')) {
            $email = $request->request->get('_email');
            $password = $request->request->get('_password');
            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            $user = $userRepository->findOneBy(['email' =>  $email]);
            if(!$user){
                return $this->render('security/login.html.twig', [
                    'last_username' => $lastUsername,
                    'error'         => "No existen usuearios con ese mail, intenta registrarte",
                ]);
            }
            if ($this->isValidCredentials($user->getEmail(), $user->getPassword(),$password, $email)) {
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->get('security.token_storage')->setToken($token);
                return $this->redirectToRoute('dashboard');
            } else {
                $lastUsername = $email;
                $error="Contraseña incorrecta";
            }
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    private function isValidCredentials($email, $password,$verifyPassword, $verifyEmail)
    {
        $passwordCadena = stream_get_contents($password);
        $email =strtolower($email);
        $verifyEmail= strtolower($verifyEmail);
        if ($verifyEmail === $email && $verifyPassword === $passwordCadena ) {
            return true;
        } else {
            return false;
        }
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
