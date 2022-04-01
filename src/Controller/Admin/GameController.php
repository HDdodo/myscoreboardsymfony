<?php

namespace App\Controller\Admin;

use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Form\GameType;
use Doctrine\ORM\EntityManagerInterface;

class GameController extends AbstractController
{
    /**
     * @Route("/admin/game",name="app_admin_game")
     */
    #[Route('/admin/game', name: 'app_admin_game')]
    public function index(GameRepository $gameRepository): Response
    {
        // On NE PEUT PAS instancier d'objets d'une classe repository. On doit les passer dans les arguments d'une méthodes d'un contrôleur.
        //NB:Pour chaque classe Entity créée, il y a une classe Repository qui correspond et qui permet de faire des requêtes SELECT sur la table correspondante.
        //$gameRepository = new GameRepository;

        return $this->render('admin/game/index.html.twig', [
            "games"=>$gameRepository->findAll()
        ]);
    }

    // /**
    //  * @Route("/admin/game",name="app_admin_game")
    //  * @Route("/admin/show/{id}",name="show")
    //  */
    // #[Route('/admin/game', name: 'app_admin_game')]
    // public function index(GameRepository $gameRepository, PlayerRepository $playerRepository, $id=null): Response
    // {
    //     //une table de bdd est correspondante à une tentité dans l'app.
    //     //Lorsque l'on souhaite récupécurer des données d'une table en BDD(requête de SELECT), il nous faut appeler le repository de l'entité (table) sur laquelle la requête a lieu
    //     //pour l'id, il nous faut une condition pour savoir si l'id est null(false) ou pas:
    //     if($id):
    //         $player=$playerRepository->find($id);
    //     else:
    //         $player=false; //si on met pas de false (et que aucun id n'est indiqué, il y aura une erreur)
    //     endif;
    //     $players= $playerRepository->findAll();
    //     //dump($players);
    //     //dd($players); //ne pas oublier de le mettre en commentaire car ca bloque le script
    //     return $this->render('admin/game/index.html.twig', [
    //         'controller_name'=>'GameController',
    //         "games"=>$gameRepository->findAll(),
    //         "players"=>$players,
    //         "player"=>$player
    //     ]);
    // }

    /**
     * @Route("/admin/game/new",name="app_admin_game_new")
     */
    public function new(Request $request, EntityManagerInterface $em)
    {
//La Classe Request permet d'instancier un objet qui contient toutes les valeurs des variables super-globales de PHP. Ces valeurs sont dans des propriétés(qui sont des objets).
//$request->query contient $_GET
//$request->request contient $_POST
//$request->server contient $SERVER, etc..
//Pour accéder aux valeurs, on utilisera sur ces propriétés la méthode ->get('indice') (qui veut dire qu'on va récupérer une valeur)

//la classe EntityManager va permettre d'éxécuter les requêtes qui modifient les données (INSERT,UPDATE,DELETE). Il va tjr utiliser des objets Entity pour modifier les données

        //dd($request->request->get('game[title]'));//là ca marche pas
        //le but est de créer un formulaire
        $jeu=new Game;
        //dump($jeu);
//On créée un objet objet$form pour gérer le formulaire. Il est créée à partir de la classe GameType. On relie ce formulaire à l'objet $jeu
        $form =$this->createForm(GameType::class, $jeu);
         

    //l'objet $form va gérer ce qui vient de la requête HTTP(avc l'objet $request)
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            //la méthode persist() prépare la requête INSERT avec les données de l'objet passé en argument
            $em->persist($jeu);
            //La méthode flush() exécute les requêtes en attente et donc modifie la base de données
            $em->flush();

            //redirection vers une route du projet (là on utilise le name, et pas la route)
            return $this->redirectToRoute("app_admin_game");
        }


        return $this->render("admin/game/form.html.twig",["formGame"=>$form->createView()
        ]);
    }
    /**
     * @Route("/admin/game/edit/{id}", name="app_admin_game_edit")
     */
    //On veut modifier un jeu qui existe déjà
    public function edit(Request $rq, EntityManagerInterface $em,GameRepository $gameRepository, $id)
    {
        $jeu = $gameRepository->find($id);
        $form = $this->createForm(GameType::class, $jeu);

        $form->handleRequest($rq); //handleRequest important
        if($form->isSubmitted()&& $form->isValid()){
            $em->flush();
            return $this->redirectToRoute("app_admin_game");
        }

        return $this->render("admin/game/form.html.twig",["formGame"=>$form->createView() ]);
    }
//2eme edit mais en modifier version php 5. Sérieusement?
    /**
     * @Route("/admin/game/modifier/{title}", name="app_admin_game_modifier")
     * Si le chemin de la route contient une partie variable (donc entre {}), on peut récupérer une objet entité
     * directement avec la valeur de cette partie de l'URL. Il faut que le nom de ce paramètre soit le nom d'une
     * propriété de la classe Entity.
     * Par exemple, le paramètre est {title}, parce que dans l'entité Game il y a une propriété title.
     * Dans les arguments de la méthode, on peut alors utiliser un objet de la classe Game ($jeu dans l'exemple)
     */
    
    public function modifier(Request $rq, EntityManagerInterface $em, Game $jeu)
    {
        //$jeu = $gameRepository->find($id); //ca on en a plus besoin
        $form = $this->createForm(GameType::class, $jeu);

        $form->handleRequest($rq); //handleRequest important
        if($form->isSubmitted()&& $form->isValid()){
            $em->flush();
            return $this->redirectToRoute("app_admin_game");
        }

        return $this->render("admin/game/form.html.twig",["formGame"=>$form->createView() ]);
    }
    

    /**
     * @Route("/admin/game/delete/{id}", name="app_admin_game_delete")
     */
    public function delete(GameRepository $gr, $id, Request $rq, EntityManagerInterface $em)
    {
        $jeu = $gr->find($id);
        if($rq->isMethod("POST")){
            $em->remove($jeu);
            $em->flush();
            return $this->redirectToRoute("app_admin_game");
        }

        return $this->render("admin/game/delete.html.twig",["game"=> $jeu] ); 
    }
    

}
