<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/api/cart', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        return $this->json($cartService->getCart(), 200, [], ['groups' => ['cart:read']]);
    }

    #[Route('/cart/add/{id}/{quantity}', name: 'app_cart_add')]
    #[Route('/api/cart/add/{id}/{quantity}', name: 'api_cart_add')]
    public function addToCart(Product $product, CartService $cartService,$quantity, Request $request): Response
    {

        $route = $request->attributes->get('_route');
        $cartService->addToCart($product, $quantity);
        if ($route == 'api_cart_add') {
            return $this->json($cartService->getCart(), 200, [], ['groups' => ['cart:read']]);
        }
        return $this->redirectToRoute('app_product');
    }


    #[\Symfony\Component\Routing\Annotation\Route('/api/cart/emptycart', name: 'api_cart_emptycart')]
    #[\Symfony\Component\Routing\Annotation\Route('/cart/emptycart', name: 'app_cart_emptycart')]
    public function emptyCart(CartService $cartService, Request $request): Response{
        $cartService->emptyCart();
        $route = $request->attributes->get('_route');
        if ($route = 'api_cart_emptycart') {
            return $this->json($cartService->getCart(), 200);
        }
        return $this->redirectToRoute('app_cart');
    }

    #[\Symfony\Component\Routing\Annotation\Route('/cart/removeOne/{id}', name:'app_cart_removeone')]
    #[\Symfony\Component\Routing\Annotation\Route('/api/cart/removeOne/{id}', name:'api_cart_removeone')]
    public function removeOne(CartService $cartService, Product $product): Response{
        $cartService->removeProduct($product);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/removerow/{id}', name: 'app_cart_removerow')]
    #[Route('/api/cart/removerow/{id}', name: 'api_cart_removerow')]
    public function removeRow(CartService $cartService, Product $product){
        $cartService->removeRow($product);
        return $this->redirectToRoute('app_cart');
    }
}
