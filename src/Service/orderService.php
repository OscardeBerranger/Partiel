<?php

namespace App\Service;

class orderService
{
    public function orderRestructurationForRender($myOrders){
        $returnable = [];
        foreach ($myOrders as $order) {
            $items = [];
            foreach ($order->getOrderItems() as $item){
                $imageUrl = null;
                if ($item->getProduct()->getImage()){
                    $imageUrl = $item->getProduct()->getImage()->getImageName();
                }
                $items[] = [
                    'id'=>$item->getId(),
                    'quantity'=>$item->getQuantity(),
                    'productName'=>$item->getProduct()->getName(),
                    'productId'=>$item->getProduct()->getId(),
                    'productPrice'=>$item->getProduct()->getPrice(),
                    'imageUrl'=>"/public/images/".$imageUrl
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
        return $returnable;
    }
}