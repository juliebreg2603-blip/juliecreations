<?php
namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/validate', name: 'app_order_validate')]
    #[IsGranted('ROLE_USER')]
    public function validate(
        SessionInterface $session,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        // Créer la commande
        $order = new Order();
        $order->setUser($this->getUser());
        $order->setCreatedAt(new \DateTimeImmutable());
        
        $total = 0;

        // Créer les lignes de commande
        foreach ($cart as $id => $item) {
            $product = $productRepository->find($id);
            if ($product) {
                $quantity = is_array($item) ? $item['quantity'] : $item;
                $instructions = is_array($item) ? ($item['instructions'] ?? '') : '';

                $orderLine = new OrderLine();
                $orderLine->setMyOrder($order);
                $orderLine->setProduct($product);
                $orderLine->setQuantity($quantity);
                $orderLine->setPrice($product->getPrice());
                $orderLine->setInstructions($instructions);

                $entityManager->persist($orderLine);
                $total += $product->getPrice() * $quantity;
            }
        }

        $order->setTotal($total);
        $entityManager->persist($order);
        $entityManager->flush();

        // Vider le panier
        $session->remove('cart');

        $this->addFlash('success', 'Votre commande a été validée avec succès !');
        
        return $this->redirectToRoute('app_order_confirmation', ['id' => $order->getId()]);
    }

    #[Route('/confirmation/{id}', name: 'app_order_confirmation')]
    #[IsGranted('ROLE_USER')]
    public function confirmation(Order $order): Response
    {
        // Vérifier que c'est bien la commande de l'utilisateur connecté
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/confirmation.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/history', name: 'app_order_history')]
    #[IsGranted('ROLE_USER')]
    public function history(): Response
    {
        $orders = $this->getUser()->getOrders();

        return $this->render('order/history.html.twig', [
            'orders' => $orders,
        ]);
    }
}
