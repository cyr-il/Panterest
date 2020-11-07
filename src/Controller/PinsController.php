<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PinRepository $pinRepository): Response

    {
        $pins = $pinRepository->findAll();
        return $this->render('pins/index.html.twig', [
            'pins' => $pins,
        ]);
    }

     /**
     * @Route("/pin/{id<[0-9]+>}", name="show_pin")
     */
    public function showPinDetail(Pin $pin): Response

    {
        return $this->render('pins/showPinDetail.html.twig', compact('pin'));
    }
}
