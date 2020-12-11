<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @Route("/user/{id}", name="user_details")
     */
    public function details(User $user = null): Response
    {
        $user ??= $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        return $this->render('user/details.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/profile/update", name="update_profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function updateProfile(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->render('user/update-profile.html.twig', [
                'profile_form' => $form->createView()
            ]);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('profile');
    }
}
