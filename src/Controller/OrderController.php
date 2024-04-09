<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{

    #[Route('/api/order/make', name: 'app_order_makeorder')]
    public function makeOrder(CartService $cartService, EntityManagerInterface $manager): Response{
        $order = new Order();
        $order->setOfUser($this->getUser());
        $order->setStatus(2);
        $order->setTotal($cartService->getTotal());
        foreach ($cartService->getCart() as $item){
            $orderItem = new OrderItem();
            $orderItem->setProduct($item['product']);
            $orderItem->setQuantity($item['quantity']);
            $orderItem->setOfOrder($order);
            $manager->persist($orderItem);
        }
        $manager->persist($order);
        $manager->flush();
        return $this->redirectToRoute('app_product');
    }


    #[Route('/pay/order/{id}', name: 'app_order_pay')]
    public function payOrder(Order $order, EntityManagerInterface $manager): Response
    {
        $order->setStatus(0);
        $manager->persist($order);
        $manager->flush();
        return $this->json("now you're poor haha", Response::HTTP_OK);
    }

}
