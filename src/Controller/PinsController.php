<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function create(Request $request, EntityManagerInterface $em): Response

    {
        $pin = new Pin;
        $form = $this->createFormBuilder($pin)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($pin);
            $em->flush();

            return $this->redirectToRoute('home');

        }
        
        return $this->render('pins/create.html.twig', [
            'monFormulaire' => $form->createView()
        ]);
    }

    
}
