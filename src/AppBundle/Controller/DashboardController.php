<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use AppBundle\Entity\Products;
use AppBundle\Entity\Categories;
use AppBundle\Form\productType;

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
        $sql = 'SELECT p.id id, p.name producto,image, c.name categoria FROM products p JOIN categories c ON c.id = p.category_id';

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
     * @Route("/newproduct", name="newproduct")
     */
    public function newProductAction(Request $request)
    {

        $product = new Products();

        $form = $this->createForm(productType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $product = $form->getData();
            $image = $product->getImage();
            $imageName = $this->generateUniqueFileName() . '.' . $image->guessExtension();
            $image->move(
                dirname($this->getParameter('kernel.root_dir')) . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'productImg',
                $imageName
            );
            

            $product->setImage($imageName);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('dashboard');
        }
        return $this->render('dashboard/formProducts.html.twig', array('form' => $form->createView()));
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }
}
