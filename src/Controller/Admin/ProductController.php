<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'admin_products_')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'Gestion des produits',
        ]);
    }
}
