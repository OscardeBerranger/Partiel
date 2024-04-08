<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $session;
    private $productRepository;

    public function __construct(ProductRepository $produitRepository, RequestStack $requestStack)
    {
        $this->productRepository = $produitRepository;
        $this->session = $requestStack->getSession();
    }

    public function getCart(){
        $cart = $this->session->get('sessionCart', []);
        $entityCart = [];

        foreach($cart as $produitId=>$quantity)
        {
            $item = [
                'product'=>$this->productRepository->find($produitId),
                'quantity'=>$quantity
            ];
            $entityCart[] = $item;
        };

        return $entityCart;
    }


    public function addToCart(Product $product, $quantity)
    {
        $cart = $this->session->get('sessionCart', []);
        if (isset($cart[$product->getId()])){
            $cart[$product->getId()] = $cart[$product->getId()]+$quantity;
        }else{
            $cart[$product->getId()]=$quantity;
        }
        $this->session->set('sessionCart', $cart);
    }

    public function getTotal(){
        $total = 0;

        foreach ($this->getCart() as $item){
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }

    public function removeProduct(Product $product){
        $cart = $this->session->get('sessionCart', []);
        $productId = $product->getId();

        if(isset($cart[$productId])){
            $cart[$productId]--;
            if ($cart[$productId]===0){
                unset($cart[$productId]);
            }
        }
        $this->session->set('sessionCart', $cart);
    }


    public function removeRow(Product $product){
        $cart = $this->session->get('sessionCart', []);
        $productId = $product->getId();

        if (isset($cart[$productId])){
            unset($cart[$productId]);
        }
        $this->session->set('sessionCart', $cart);
    }

    public function emptyCart(){
        $this->session->remove('sessionCart');
    }
}