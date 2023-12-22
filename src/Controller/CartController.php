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
        // Trouver le produit dans la base de données
        $product = $this->em->getRepository(Product::class)->find($productId);

        // Vérifier que le produit existe
        if (!$product) {
            throw $this->createNotFoundException('Le produit n\'a pas été trouvé');
        }

        $user = $this->getUser(); // récupère l'utilisateur actuellement connecté

        // vérifie si un utilisateur est connecté
        if (!$user) {
            throw new \Exception('Vous devez être connecté pour ajouter des produits au panier');
        }

        // Récupération du panier depuis la session si possible, sinon depuis l'utilisateur
        $cartId = $this->session->get('cartId');
        if ($cartId) {
            $cart = $this->em->getRepository(Cart::class)->find($cartId);
        } else {
            // Vérifiez si l'utilisateur a déjà un panier
            $cart = $user->getCart();

            // Création d'un panier si l'utilisateur n'en a pas
            if (!$cart) {
                $cart = new Cart();
                $cart->setCreatedAt(new \DateTimeImmutable());
                $cart->setIsPurshased(false);

                $cart->setUser($user); // lien entre le panier et l'utilisateur

                $this->em->persist($cart);
                $this->em->flush();
            }

            // Enregistrez l'ID du Cart dans la session
            $this->session->set('cartId', $cart->getId());
        }

        // Ajout du produit au panier
        $this->cartService->addToCart($cart, $product);

        // Redirection vers la page du panier
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
        $cart = null;

        if (!$cartId && $this->getUser()) {
            // Si l'ID du panier n'est pas en session et que l'utilisateur est connecté,
            // on récupère le panier de l'utilisateur.
            $cart = $this->getUser()->getCart();
        } elseif ($cartId) {
            // Sinon, si l'ID du panier est en session, on récupère le panier par cet ID.
            $cart = $this->em->getRepository(Cart::class)->find($cartId);
        }

        if (!$cart) {
            // Si aucun panier n'a été trouvé, vous pouvez soit en créer un nouveau, soit rediriger vers une autre page.
            $cart = new Cart();
            $this->em->persist($cart);
            $this->em->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
            'total' => $this->cartService->calculateCartTotal($cart),
        ]);
    }

    #[Route('/cart/update/{id}', name: 'cart_update_quantity', methods: ["POST"])]
    public function updateQuantity(CartItem $cartItem, Request $request): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $newQuantity = $data['quantity'] ?? null;

        if ($newQuantity !== null && is_numeric($newQuantity) && $newQuantity > 0) {
            $cartItem->setQuantity($newQuantity);
            $this->em->persist($cartItem);
            $this->em->flush();

            // Return a success message
            return $this->json(['status' => 'success', 'message' => 'Quantité mise à jour.']);
        }

        // Return an error message
        return $this->json(['status' => 'error', 'message' => 'Quantité invalide.']);
    }
}