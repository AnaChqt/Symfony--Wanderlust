<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PinsController extends AbstractController
{
    public function index(PinRepository $pinRepository, PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $data = $pinRepository->findBy([], ['createdAt' => 'DESC']); //Permet d'afficher les pins par oredre de création du plus recent au plus vieux  ,2 après le crochet permet d'affiher que les 2 derniers pin par ex
        $pins = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('pins/index.html.twig', [
            'pins' => $pins
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user.isVerified()")
     */
    public function create(Request $request, EntityManagerInterface $em, UserRepository $userRepo): Response
    {
        $pin = new Pin;

        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin successfully created !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function show(Pin $pin): Response
    {
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @IsGranted("PIN_MANAGE", subject="pin")
     */
    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Pin successfully updated !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }

    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('PIN_MANAGE', $pin);

        if ($this->isCsrfTokenValid('pins_deletion_' . $pin->getId(), $request->request->get('csrf_token'))) {
            $em->remove($pin);
            $em->flush();

            $this->addFlash('info', 'Pin successfully deleted !');
        }

        return $this->redirectToRoute('app_home');
    }
}
