<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CommencerPartieType;
use App\Entity\Contest;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GameRepository $gr, PlayerRepository $pr): Response
    {
        return $this->render('home/index.html.twig', [
            'jeux' => $gr -> findAll(),
            'nb_joueurs'=> count($pr->findAll()),
            'gagnants'=>$pr->findWinners()
        ]);
    }

    #[Route('/commencer-une-partie-{title}', name:'app_home_contest')]
    public function commencer(Game $jeu, EntityManagerInterface $em, Request $rq)
    {
        $partie=new Contest;
        $partie->setGame($jeu);
        //maintenant on peut créer notre formulaire:
        $form = $this->createForm(CommencerPartieType::class, $partie);
        //il faut récupérer la requête http, dc on rajoute Request
        $form->handleRequest($rq);
        if($form->isSubmitted() && $form->isValid() ){
            $em->persist($partie);
            $em->flush();
            $this->addFlash("success", "La nouvelle partie a bien été enregistrée");
            //$this->addFlash("danger","Message d'erreur"); //on peut aussi mettre info...
            return $this->redirectToRoute("app_home");
        }
        return $this->render("home/commencer.html.twig", [
            "form"=> $form->createView()
        ]);
    }

}
