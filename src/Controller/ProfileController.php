<?php

namespace App\Controller;

use App\Form\EditProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $currentPassword = $form->get('currentPassword')->getData();
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('error', 'Current password is incorrect.');
            } else {

                $user->setEmail($form->get('email')->getData());

                $newPassword = $form->get('newPassword')->getData();
                if ($newPassword) {
                    $user->setPassword(
                        $passwordHasher->hashPassword(
                            $user,
                            $newPassword
                        )
                    );
                }
                $user->setUpdatedAt(new \DateTimeImmutable());
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Profile updated successfully.');

                return $this->redirectToRoute('app_profile_edit');
            }
        }

        return $this->render('edit/edit.html.twig', [
            'editProfileForm' => $form->createView(),
        ]);
    }
}