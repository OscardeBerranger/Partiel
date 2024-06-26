<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Service\QRCodeService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


class ProductController extends AbstractController
{
    #[Route('/api/product', name: 'app_product')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->json($productRepository->findAll(), Response::HTTP_OK, [], ['groups' => ['cart:read']]);
    }

    #[Route('/admin/product/create', name: 'app_product_create')]
    public function createProduct(Request $request, EntityManagerInterface $manager): Response{
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('app_product_create');
        }
        return $this->render('product/create.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/admin/product/delete/{id}', name: 'app_product_delete')]
    public function deleteProduct(Product $product, EntityManagerInterface $manager){
        $manager->remove($product);
        $manager->flush();
        return $this->redirectToRoute('app_product');
    }

    #[Route('/admin/product/show/{id}', name: 'app_product_temp')]
    public function temp(Product $product){
        return $this->render('product/temp.html.twig', [
            "product" => $product,
        ]);
    }

    #[Route('/product/getQrCode/{id}', name: 'app_product_getQrCode')]
    public function getProductQrCode(Product $product, QRCodeService $service, Request $request): Response
    {
        $qr = $service->createQrCode("http://localhost:8000/api/cart/add/".$product->getId()."/1");
        $product->setQrCode($qr);
        return $this->render('product/qrCode.html.twig', [
            "simple" => $qr,
        ]);
    }

    #[Route('/product/getQrCodeAsPdf/{id}', name: 'app_product_getQrCode_asPdf')]
    public function getProductQrCodeAsPdf(Product $product, Pdf $pdf, QRCodeService $service): Response
    {
        $qrcode =  $this->renderView('product/qrCode.html.twig', [
            "simple" => $product->getQrCode(),
        ]);
        return new PdfResponse(
            $pdf->getOutputFromHtml($qrcode),
            $product->getName().".pdf"
        );
    }

}
