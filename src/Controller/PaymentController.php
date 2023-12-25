<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentController extends AbstractController
{
    private string $stripeSecretKey;

    public function __construct(ParameterBagInterface $params, EntityManagerInterface $em)
    {
        $this->stripeSecretKey = $params->get('stripe_secret_key');
        $this->em = $em;
    }

    #[Route('/order/create-session-stripe/{id}', name: 'payment_stripe')]
    public function stripeCheckout($id): RedirectResponse
    {
        $stripe = new \Stripe\StripeClient($this->stripeSecretKey);
        $order = $this->em->getRepository(Order::class)->findOneBy(['id' => $id]);
        dd($order);



        return new RedirectResponse($checkout_session->url, 303);
    }
}