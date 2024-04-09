<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\orderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MyOrderController extends AbstractController
{
    #[Route('/api/get/my/order', name: 'app_get_my_order')]
    public function getMyOrder(OrderRepository $repository, orderService $service): Response
    {
        $myOrder = $repository->findBy(['ofUser' => $this->getUser()]);

        return $this->json($service->orderRestructurationForRender($myOrder), Response::HTTP_OK, [], ['groups' => ['order:read']]);
    }
}
