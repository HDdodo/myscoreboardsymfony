<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/recherche', name: 'app_search')]
    public function index(Request $rq, GameRepository $gr): Response
    {
        //pour vérifier si la recherche marche avec request
        //dd($rq->query->get("search"));
        $word =$rq->query->get("search");
        $jeux =$gr->findBySearch($word);

        return $this->render('search/index.html.twig', [ //'home/index.html.twig'pour l'exercice search doit marcher
            'jeux' => $jeux,
            //'nb_joueurs'=>null
            'mot'=>$word
        ]);
    }
}
/* EXO 1. afficher les resultats dans le fichier search/index.html.twig sous forme de card
                afficher aussi dans une balise h1 : Résultat de la recherche pour ...(on a copié l'index de home ds template)
                et remplacer les ... par le mot tapé dans la barre de recherche 
  2.  trouvez comment utiliser le même code pour les 
  		card pour le fichier home/index.html.twig
        et le fichier search/index.html.twig
 */
