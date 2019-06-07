<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PadawanController extends AbstractController
{
    /**
     * @Route("/padawan/profile/{id}", name="padawan_profile")
     */
    public function profil()
    {
        return $this->render('padawan/profil.html.twig', [
            'controller_name' => 'PadawanController',
        ]);
    }
}
