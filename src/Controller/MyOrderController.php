<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyOrderController extends AbstractController
{
    #[Route('/api/get/my/order', name: 'app_get_my_order')]
    public function getMyOrder(OrderRepository $repository): Response
    {
        $myOrder = $repository->findBy(['ofUser' => $this->getUser()]);
        $returnable = [];
        foreach ($myOrder as $order) {
            $items = [];
            foreach ($order->getOrderItems() as $item){
                $items[] = [
                    'id'=>$item->getId(),
                    'quantity'=>$item->getQuantity(),
                    'productName'=>$item->getProduct()->getName(),
                    'productId'=>$item->getProduct()->getId(),
                    'productPrice'=>$item->getProduct()->getPrice(),
                ];
            }
            $returnable[] = [
                'id' => $order->getId(),
                'ofUser' => $order->getOfUser(),
                'total' => $order->getTotal(),
                'status' => $order->getStatus(),
                'products' => $items,
            ];
        }
        return $this->json($returnable, Response::HTTP_OK, [], ['groups' => ['order:read']]);
    }
}
