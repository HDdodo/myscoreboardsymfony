<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test-route", name="app_test")
     */
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        //->render va générer de l'affichage. Le 1er arg : c'est une vue, avc syntaxe particulière. 2eme arg : c'est un array, par défaut il est vide.
        return $this->render('test/index.html.twig', [
            'controller_name' => 'bonjour', 'texte' =>"croise les doigts pour que ça marche"
        ]);
    }
    /*
    Exercice :
    Ajouter une route pour le chemin "/test/calcul" qui utilise le fichier test/index.html.twig et qui affiche le résultat de 12+7
    */
    /**
     * @Route("/test/calcul")
     */
    #[Route('/test/calcul')]
    public function calcul(): Response
    {
        $a = 12;
        $b = 7;
        return $this->render("test/index.html.twig", [
            "controller_name"=> "HDora",
            "texte"=>"Pétage de cable total!!",
            "calcul"=>$a+$b
        ]);
    }

    /**
     * Exercice :Ajouter une route (dans le controleur Test)
    *url "/test/salut"
    *methode "salut"
    *dans un nouvel affichage (donc un nouveau fichier twig)
    *Afficher "Salut $prenom" (changer le contenu du block body, ne pas changer le contenu des autres blocks)
     */
    /**
     * @Route("/test/salut")
     */
    #[Route('/test/salut')]
    public function salut()
    {
        return $this->render("test/salut.html.twig",["prenom"=>"HDora"]);
    }
    //t'es obligé de mettre @Route("") dans commentaires juste au-dessus d'une nouvelle route, sinon ca marche pas !!!
    /**
     * @Route("/test/tableau")
     */
    public function tableau()
    {
        $array =["bonjour", "je m'appelle",789, true, 12, 38];
        return $this->render("test/tableau.html.twig", ["tableau"=>$array]);
    }

    //tableau associatif
    /**
     * @Route("/test/tableau-assoc")
     */
    public function tab()
    {
       $p = [
           "nom"=>"Cérien",
           "prenom"=>"Jean",
           "age"=>34
       ];
       return $this->render("test/assoc.html.twig", ["personne"=>$p]);
       //Exercice : afficher "je m'appelle" suivi du prénom et du nom dans le tableau
    }
    
    //la route : est différent du fichier : @route désigne l'url
    /**
     * @Route("/test/objet")
     */
    public function objet()
    {
        $objet= new \stdClass;
        $objet->prenom="Nordine";
        $objet->nom="Ateur";
        $objet->age=40;
        return $this->render("test/assoc.html.twig", ["personne"=> $objet]);
    }
    


}
