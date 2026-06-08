<?php
namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{#[Route('/', name: 'app_cart_index')]
public function index(SessionInterface $session, ProductRepository $productRepository): Response
{
    $cart = $session->get('cart', []);
    $cartData = [];
    $total = 0;

    foreach ($cart as $id => $item) {
        $product = $productRepository->find($id);
        if ($product) {
            $quantity = is_array($item) ? $item['quantity'] : $item;
            $instructions = is_array($item) ? ($item['instructions'] ?? '') : '';
            
            $cartData[] = [
                'product' => $product,
                'quantity' => $quantity,
                'instructions' => $instructions,
            ];
            $total += $product->getPrice() * $quantity;
        }
    }

    return $this->render('cart/index.html.twig', [
        'items' => $cartData,
        'total' => $total,
    ]);
}

   #[Route('/add/{id}', name: 'app_cart_add')]
public function add(int $id, Request $request, SessionInterface $session): Response
{
    $cart = $session->get('cart', []);
    $instructions = $request->request->get('instructions', '');

    if (!empty($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            'quantity' => 1,
            'instructions' => $instructions,
        ];
    }

    $session->set('cart', $cart);
    $this->addFlash('success', 'Produit ajouté au panier !');

    return $this->redirectToRoute('app_product_index');
}

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart_index');
    }
}
