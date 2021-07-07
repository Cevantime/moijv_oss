<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @Route("/user/{id}", name="user_details")
     */
    public function details(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder, User $user = null): Response
    {
        $user ??= $this->getUser();

        if( ! $user) {
            return $this->redirectToRoute('login');
        }

        $profileForm = $this->createForm(ProfileFormType::class, $user);

        $profileForm->handleRequest($request);

        if($profileForm->isSubmitted() && $profileForm->isValid()) {
            if(!$profileForm->get('plainPassword')->isEmpty()) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $profileForm->get('plainPassword')->getData()
                    )
                );
            }
            $manager->persist($user);
            $manager->flush();
            return $this->redirect('profile');
        }

        return $this->render('user/details.html.twig', [
            'user' => $user,
            'profile_form' => $profileForm->createView()
        ]);
    }
}
