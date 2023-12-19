<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/products', name: 'admin_products_')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/product/index.html.twig');
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // On crée un nouveau produit
        $product = new Product();

        // On crée le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);

        // On traite la requête du formulaire
        $productForm->handleRequest($request);

            // On vérifie si le formulaire est soumis et valide
            if ($productForm->isSubmitted() && $productForm->isValid()) {
                // Générer le slug à partir du label du produit
                $slug = $slugger->slug($product->getProductLabel());
                $product->setSlug($slug);

                // Enregistrer le produit en base de données
                $em->persist($product);
                $em->flush();

                // Rediriger vers la page d'index des produits
                return $this->redirectToRoute('admin_products_index');
            }

        return $this->render('admin/product/add.html.twig', [
            'productForm' => $productForm->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, $id): Response
    {
        // On crée le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);

        // On traite la requête du formulaire
        $productForm->handleRequest($request);

        // On vérifie si le formulaire est soumis et valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // Générer le slug à partir du label du produit
            $slug = $slugger->slug($product->getProductLabel());
            $product->setSlug($slug);

            // Enregistrer le produit en base de données
            $em->persist($product);
            $em->flush();

            // Rediriger vers la page d'index des produits
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/product/edit.html.twig', [
            'productForm' => $productForm->createView(),
            'product' => $product
        ]);    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Product $product): Response
    {
        return $this->render('admin/product/index.html.twig');
    }

}
