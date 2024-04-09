<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->json($productRepository->findAll(), Response::HTTP_OK, [], ['groups' => ['cart:read']]);
    }

    #[Route('/product/create', name: 'app_product_create')]
    public function createProduct(Request $request, EntityManagerInterface $manager): Response{
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('app_product');
        }
        return $this->render('product/create.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/product/temp/{id}', name: 'app_product_temp')]
    public function temp(Product $product){
        return $this->render('product/temp.html.twig', [
            "product" => $product,
        ]);
    }
}
