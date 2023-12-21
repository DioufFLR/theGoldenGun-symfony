<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users', name: 'admin_users_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'controller_name' => 'Gestion des Utilisateurs',
            'users' => $users
        ]);
    }

    #[Route('/add', name: 'add')]
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');


        $user = new User();

        // Handle form submission
        $form = $this->createForm(UserTypeForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the password
            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);

            // Save the user
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect to the user list
            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/user/add.html.twig', [
            'controller_name' => 'Création d\'un Utilisateur',
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        // Delete the user
        $entityManager->remove($user);
        $entityManager->flush();

        // Redirect to the user list
        return $this->redirectToRoute('admin_users_index');
    }
}
