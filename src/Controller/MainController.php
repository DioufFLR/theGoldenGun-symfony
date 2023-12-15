<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
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

    #[Route('/categories/{category}', name: 'app_categories')]
    public function categories(Category $category): Response{
        return $this->render('main/categories.html.twig', [
            'category' => $category
        ]);
    dd($category);
    }

}
