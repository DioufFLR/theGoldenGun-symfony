<?php

namespace App\Controller;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment as TwigEnvironment;

class PaymentController extends AbstractController
{
    private string $stripeSecretKey;
    private UrlGeneratorInterface $router;
    private RequestStack $requestStack;
    private TwigEnvironment $twig;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em, UrlGeneratorInterface $router, RequestStack $requestStack, TwigEnvironment $twig)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
        $this->stripeSecretKey = $params->get('stripe_secret_key');
        $this->em = $em;
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/order/create-session-stripe/{id}', name: 'payment_stripe')]
    public function stripeCheckout($id): RedirectResponse
    {
        $productStripe = [];

        $cart = $this->em->getRepository(Cart::class)->find($id);

        // Si le panier n'existe pas, nous redirigeons sur le panier
        if (!$cart) {
            return $this->redirectToRoute('cart_show');
        }

        foreach ($cart->getCartItems() as $cartItem) {
            $product = $cartItem->getProduct();
            $productStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product->getProductLabel(),
                        'images' => [$this->requestStack->getCurrentRequest()->getSchemeAndHttpHost() . '/assets/img/' . $product->getProductImage()],
                    ],
                    'unit_amount' => $product->getProductPrice() * 100,
                ],
                'quantity' => $cartItem->getQuantity(),
            ];
        }

        $stripe = new StripeClient($this->stripeSecretKey);

        $checkout_session = $stripe->checkout->sessions->create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => $productStripe,
            'mode' => 'payment',
            'success_url' => 'http://localhost:4242/success',
            'cancel_url' => 'http://localhost:4242/cancel',
        ]);

        return new RedirectResponse($checkout_session->url, 303);
    }
}