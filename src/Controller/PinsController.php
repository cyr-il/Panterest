<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="home", methods="GET")
     */
    public function index(PinRepository $pinRepository): Response

    {
        $pins = $pinRepository->findBy([], ['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', [
            'pins' => $pins,
        ]);
    }

     /**
     * @Route("/pin/{id<[0-9]+>}", name="show_pin", methods="GET")
     */
    public function showPinDetail(Pin $pin): Response

    {
        return $this->render('pins/showPinDetail.html.twig', compact('pin'));
    }

    /**
     * @Route("/pin/create", name="pin_create", methods={"GET","POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, FlashyNotifier $flashy): Response

    {
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();
            $this->addFlash('success', 'Votre pin a été crée avec succès !');

            return $this->redirectToRoute('home');

        }
        
        return $this->render('pins/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

     /**
     * @Route("/pin/{id<[0-9]+>}/edit", name="pin_edit", methods={"GET","PUT"})
     */
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em, FlashyNotifier $flashy): Response

    {
        $form = $this->createForm(PinType::class, $pin, ['method'=> 'PUT']);
            
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Votre pin a été modifié avec succès !');

            return $this->redirectToRoute('home');
        }
        
        return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
            'form' => $form->createView()
        ]);
    }

    /**
    * @Route("/pin/{id<[0-9]+>}", name="pin_delete", methods="DELETE")
    */
    public function delete(Pin $pin, EntityManagerInterface $em, Request $request, FlashyNotifier $flashy): Response
    {
        $data = $request->request->get('csrf_token');

        if($this->isCsrfTokenValid('pin_delete_' . $pin->getId(), $data)) {
            $em->remove($pin);
            $em->flush();
            $this->addFlash('success', 'Votre pin a été supprimé avec succès !');
        }
        return $this->redirectToRoute('home');
    }

}
