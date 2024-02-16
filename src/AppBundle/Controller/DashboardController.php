<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;


use AppBundle\Entity\Products;
use AppBundle\Entity\Categories;
use AppBundle\Form\categoriesType;
use AppBundle\Form\productType;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Obtener la conexión de la base de datos
        $connection = $em->getConnection();

        // Crear la consulta SQL
        $sql = 'SELECT p.id id, p.name producto,image,p.deleted deleted,  c.name categoria FROM products p JOIN categories c ON c.id = p.category_id';

        // Ejecutar la consulta
        $statement = $connection->prepare($sql);
        $statement->execute();

        // Obtener los resultados
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

        return $this->redirectToRoute('dashboard');
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('dashboard/categories.html.twig', array('form' => $form->createView()));
    }
}
