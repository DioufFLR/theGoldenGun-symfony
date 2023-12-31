<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
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
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('admin/product/index.html.twig', [
            'products' => $products
        ]);
    }


    /**
     * Adds a new product.
     *
     * @param Request $request The request object.
     * @param EntityManagerInterface $em The EntityManagerInterface used to persist the product.
     * @param SluggerInterface $slugger The SluggerInterface used to generate the product slug.
     *
     * @return Response The response object.
     */
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

            /** @var UploadedFile $file */
            $file = $productForm->get('productImage')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('images_directory'), $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $product->setProductImage($newFilename);
            }

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
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // On crée le formulaire
        $productForm = $this->createForm(ProductFormType::class, $product);

        // On traite la requête du formulaire
        $productForm->handleRequest($request);

        // On vérifie si le formulaire est soumis et valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {

            /** @var UploadedFile $file */
            $file = $productForm->get('productImage')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();
                try {
                    $file->move($this->getParameter('images_directory'), $newFilename);
                } catch (FileException $e) {
                    //... handle exception if something happens during file upload
                }
                $product->setProductImage($newFilename);
            }

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
            'product' => $product]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('admin_products_index');
    }

}
