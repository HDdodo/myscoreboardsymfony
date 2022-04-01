<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        //Restreindre les routes qui commencent par /profil aux utilisateurs qui ont le rôle ROLE_PLAYER (on voit ca dans le dossier config/packages/security.yaml)
        

        return $this->render('profil/index.html.twig');
    }
    
}
