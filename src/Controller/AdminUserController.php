<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/users')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_users_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_users_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $roles = $request->request->get('roles', []);

            if ($email && $password) {
                $user->setEmail($email);
                $user->setPassword($passwordHasher->hashPassword($user, $password));
                $user->setRoles($roles);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'User created successfully.');
                return $this->redirectToRoute('admin_users_index');
            }
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_users_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $roles = $request->request->get('roles', []);

            if ($email) {
                $user->setEmail($email);
                if ($password) {
                    $user->setPassword($passwordHasher->hashPassword($user, $password));
                }
                $user->setRoles($roles);

                $entityManager->flush();

                $this->addFlash('success', 'User updated successfully.');
                return $this->redirectToRoute('admin_users_index');
            }
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'admin_users_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Prevent deleting the last admin user
            $adminUsers = $entityManager->getRepository(User::class)->count(['roles' => ['ROLE_ADMIN']]);
            if ($adminUsers > 1 || !in_array('ROLE_ADMIN', $user->getRoles())) {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'User deleted successfully.');
            } else {
                $this->addFlash('error', 'Cannot delete the last admin user.');
            }
        }

        return $this->redirectToRoute('admin_users_index');
    }
} 