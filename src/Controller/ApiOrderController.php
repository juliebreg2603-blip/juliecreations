<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/orders')]
class ApiOrderController extends AbstractController
{
    #[Route('/{userId}', name: 'api_orders_list', methods: ['GET'])]
    public function list(int $userId, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($userId);
        
        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $orders = $user->getOrders();
        $orderData = [];
        
        foreach ($orders as $order) {
            $createdAt = $order->getCreatedAt();
            $orderData[] = [
                'id' => $order->getId(),
                'total' => $order->getTotal(),
                'createdAt' => $createdAt ? $createdAt->format('Y-m-d H:i:s') : null,
                'linesCount' => count($order->getOrderLines()),
            ];
        }

        return $this->json(['orders' => $orderData]);
    }
}
