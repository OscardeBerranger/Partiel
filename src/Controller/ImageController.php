<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/admin/image/{id}', name: 'app_image')]
    public function index(Product $product): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        return $this->render('image/index.html.twig', [
            'form' => $form->createView(),
            'product' => $product
        ]);
    }

    #[Route('/image/create/{id}', name: 'app_image_create')]
    public function create(Request $request, Product $product, EntityManagerInterface $manager): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image->setProduct($product);
            $manager->persist($image);
            $manager->flush();
        }

        return $this->redirectToRoute('app_image', ['id' => $product->getId()]);
    }
}
