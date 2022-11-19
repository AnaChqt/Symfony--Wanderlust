<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    // /**
    //  * @IsGranted("ROLE_USER")
    //  */
    public function show(): Response
    {
        // if (!$this->getUser()) {
        //     $this->addFlash('error', 'You need to log in first');
        //     return $this->redirectToRoute('app_login');
        // }

        return $this->render('account/show.html.twig', []);
    }

    // /**
    //  * @IsGranted("IS_AUTHENTICATED_FULLY")
    //  */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Account updated successfully !');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // /**
    //  * @IsGranted("IS_AUTHENTICATED_FULLY")
    //  */
    public function editPassword(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'current_password_is_required' => true
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $form['plainPassword']->getData()),
            );
            $em->flush();

            $this->addFlash('success', 'Password updated successfully !');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/editPassword.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
