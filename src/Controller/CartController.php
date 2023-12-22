<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    private CartService $cartService;
    private $session;
    private EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack, CartService $cartService, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->cartService = $cartService;
        $this->session = $requestStack->getSession();
    }

    #[Route('/cart/add/{productId}', name: 'cart_add', methods: ["GET", "POST"])]
    public function addToCart(Request $request, $productId): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $product = $this->em->getRepository(Product::class)->find($productId);

        if (!$product) {
            throw $this->createNotFoundException('Le produit n\'a pas été trouvé');
        }

        $cartId = $this->session->get('cartId');
        if ($cartId) {
            $cart = $this->em->getRepository(Cart::class)->find($cartId);
        } else {
            $cart = new Cart();
            $this->em->persist($cart);
            $this->em->flush();

            // Enregistrez l'ID du Cart dans la session
            $this->session->set('cartId', $cart->getId());
        }

        $this->cartService->addToCart($cart, $product);

        // Redirige vers la page du panier
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/remove/{productId}', name: 'cart_remove', methods: ["GET", "POST"])]
    public function removeFromCart(Request $request, $cartItemId): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $cartId = $this->session->get('cartId');
        if (!$cartId) {
            throw $this->createNotFoundException('Le panier n\'a pas été trouvé');
        }

        $cart = $this->em->getRepository(Cart::class)->find($cartId);

        $cartItem = $this->em->getRepository(CartItem::class)->find($cartItemId);
        if (!$cartItem) {
            throw $this->createNotFoundException('Le produit du panier n\'a pas été trouvé');
        }

        $this->cartService->removeFromCart($cart, $cartItem);

        // Redirige vers la page du panier
        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart', name: 'cart_show', methods: ["GET"])]
    public function showCart(): \Symfony\Component\HttpFoundation\Response
    {
        $cartId = $this->session->get('cartId');
        if (!$cartId) {
            throw $this->createNotFoundException('Le panier n\'a pas été trouvé');
        }

        $cart = $this->em->getRepository(Cart::class)->find($cartId);

        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
            'total' => $this->cartService->calculateCartTotal($cart),
        ]);
    }
}