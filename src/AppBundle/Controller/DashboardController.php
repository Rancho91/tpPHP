<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use AppBundle\Entity\Products;
use AppBundle\Entity\Categories;
use AppBundle\Entity\Users;
use AppBundle\Form\categoriesType;
use AppBundle\Form\editPerfilType;
use AppBundle\Form\productType;
use AppBundle\Form\registerType;

class DashboardController extends Controller
{

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $connection = $em->getConnection();
        $sql = 'SELECT p.id id, p.name producto,image,p.deleted deleted,  c.name categoria FROM products p JOIN categories c ON c.id = p.category_id';

        $statement = $connection->prepare($sql);
        $statement->execute();

        $results = $statement->fetchAll();
        $repository = $this->getDoctrine()->getRepository(Categories::class);
        $categories =  $repository->findAll();
        // var_dump($products);
        // replace this example code with whatever you need
        return $this->render('dashboard/dashboard.html.twig', array('products' => $results, 'categories' => $categories));
    }
    /**
     * @Route("/dashboard/newproduct/{id}", name="newproduct")
     */
    public function newProductAction(Request $request, $id = null)
    {
        $imageProduct = null;
        if ($id) {
            $repository = $this->getDoctrine()->getRepository(Products::class);
            $product = $repository->find($id);
            $imageProduct = $product->getImage();
            $product->setImage(null);
        } else {
            $product = new Products();
            $product->setDeleted(0);
        }
        $form = $this->createForm(productType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $product = $form->getData();
            if (is_null($product->getImage()) && !$imageProduct) {
                $this->addFlash('imageError', 'Debe proporcionar una imagen.');
            } else {
                if (is_null($product->getImage())) {
                    $imageName = $imageProduct;
                } else {
                    $image = $product->getImage();
                    $imageName = $this->generateUniqueFileName() . '.' . $image->guessExtension();
                    $image->move(
                        dirname($this->getParameter('kernel.root_dir')) . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'productImg',
                        $imageName
                    );
                }
                $product->setImage($imageName);
                $em = $this->getDoctrine()->getManager();
                $em->persist($product);
                $em->flush();
                return $this->redirectToRoute('dashboard');
            }
        }
        return $this->render('dashboard/formProducts.html.twig', array('form' => $form->createView(), 'image' => $imageProduct));
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/dashboard/delete/{id}", name="deleted")
     */
    public function deleteProductAction(Request $request, $id)
    {
        if ($id) {
            $repository = $this->getDoctrine()->getRepository(Products::class);
            $product = $repository->find($id);
            if ($product->getDeleted() == 1) {
                $product->setDeleted(0);
            } else {
                $product->setDeleted(1);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }

        if (!$product) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        return $this->json([
            'success' => true,
            'state' => $product->getDeleted() == 1 ? "disabled" : "enabled"
        ]);
    }

    /**
     * @Route("/dashboard/categories/{id}", name="categoriesCreate")
     */
    public function CategoriesAction(Request $request, $id = null)
    {
        if ($id) {
            $repository = $this->getDoctrine()->getRepository(Categories::class);
            $category = $repository->find($id);
        } else {
            $category = new Categories();
        }
        $form = $this->createForm(categoriesType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $category = $form->getData();
            $category->setDeleted(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('dashboard/categories.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/dashboard/tale/categories", name="categories")
     */
    public function categoriesTableAction(Request $request)
    {

        $repository = $this->getDoctrine()->getRepository(Categories::class);
        $categories =  $repository->findAll();
        // var_dump($products);
        // replace this example code with whatever you need
        return $this->render('dashboard/tableCategories.html.twig', array('categories' => $categories));
    }

    /**
     * @Route("/dashboard/deleteCategory/{id}", name="deletedCategory")
     */
    public function deleteCategoryAction(Request $request, $id)
    {
        if ($id) {
            $repository = $this->getDoctrine()->getRepository(Categories::class);
            $category = $repository->find($id);
            if ($category->getDeleted() == 1) {
                $category->setDeleted(0);
            } else {
                $category->setDeleted(1);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
        }

        if (!$category) {
            throw $this->createNotFoundException('Producto no encontrado');
        }

        return $this->json([
            'success' => true,
            'state' => $category->getDeleted() == 1 ? "disabled" : "enabled"
        ]);
    }

    /**
     * @Route("/dashboard/user/{id}", name="perfil")
     */

    public function perfilAction(Request $request, $id)
    {
        $user = new Users();
        $userRepository = $this->getDoctrine()->getRepository(Users::class);
        $user = $userRepository->find($id);

        $form = $this->createForm(editPerfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $userRepository = $this->getDoctrine()->getRepository(Users::class);
            if (strlen($user->getPlainPassword()) < 8 || !preg_match('/[A-Z]+/', $user->getPlainPassword()) || !preg_match('/[0-9]+/', $user->getPlainPassword())) {
                $this->addFlash('errorRegister', 'El password debe contener una letra mayuscula, almenos un numero y de almenos 8 caracteres');
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
