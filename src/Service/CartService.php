<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addToCart(Cart $cart, Product $product, int $quantity = 1): void
    {
        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity($quantity);
        $cartItem->setCart($cart);

        $cart->addCartItem($cartItem);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function removeFromCart(Cart $cart, CartItem $cartItem): void
    {
        $cart->removeCartItem($cartItem);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    public function calculateCartTotal(Cart $cart): float|int
    {
        $total = 0;
        foreach ($cart->getCartItems() as $item){
            $total += $item->getProduct()->getProductPrice() * $item->getQuantity();
        }

        return $total;
    }
}