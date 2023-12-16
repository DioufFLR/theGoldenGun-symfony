<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

//    Affiche toutes les catégories parentes
    #[Route('/', name: 'app_main')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'categories' => $categoryRepository->findBy([
                'parent' => null
            ])
        ]);
    }

//    Sous-catégories d'une catégorie parent
    #[Route('/categories/{category}', name: 'app_categories')]
    public function categories(Category $category): Response
    {
        return $this->render('main/categories.html.twig', [
            'category' => $category
        ]);
    }

//    Produits associés a une sous-catégorie
    #[Route('/products/{category}', name: 'app_products')]
    public function products(Category $category): Response
    {
        return $this->render('main/products.html.twig', [
            'category' => $category,
        ]);
    }

// Détails d'un produit
    #[Route('/detail/{product}', name: 'app_detail')]
    public function detail(Product $product): Response
    {
        return $this->render('main/detail.html.twig', [
            'product' => $product
        ]);
    }
}