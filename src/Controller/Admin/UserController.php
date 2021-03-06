<?php

namespace App\Controller\Admin;

use App\Entity\Player;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as Hasher;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, Hasher $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if( !in_array("ROLE_ADMIN", $user->getRoles() )){ //s'il n'y a pas le role admin
            $newPlayer = new Player;
            $newPlayer->setNickname($user->getPseudo() );
            $newPlayer->setEmail($form->get("email")->getData() );
            //autre solution avec l'objet request: $newPlayer->setEmail($request->request->get("email") );
            //pr au-dessus : pas besoin de getData, on peut récupérer la valeur du champ avec l'obj request
            $user->setPlayer($newPlayer); //permet de relier user et player
        }else{ //pour être sûre que l'admin n'ait que le rôle admin
            $user->setRoles(["ROLE_ADMIN"]);
        }
        //faire ca avant l'enregistrement bdd

            $mdp = $form->get('password')->getData();
            $password =$hasher->hashPassword($user, $mdp);
            $user->setPassword($password);
            $userRepository->add($user);
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'], requirements:['id'=>'[0-9]+'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository, Hasher $hasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if( !in_array("ROLE_ADMIN", $user->getRoles() )){ //s'il n'y a pas le role admin
                $newPlayer = new Player;
                $newPlayer->setNickname($user->getPseudo() );
                $newPlayer->setEmail($form->get("email")->getData() );
                $user->setPlayer($newPlayer); //permet de relier user et player
            }else{ //pour être sûre que l'admin n'ait que le rôle admin
                $user->setRoles(["ROLE_ADMIN"]);
            }

            if( $mdp =$form->get('password')->getData() ){
                $password =$hasher->hashPassword($user, $mdp);
                $user->setPassword($password);
            }
            $userRepository->add($user);
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    /*
    Exos :
    Rajouter une route: name='app_admin_user_update'
    Ds cette route, récupérer tous les users. Vérifier pour chaque user s'il y a un player relié ou pas.
    S'il n'y a pas de player relié, créer un nouveau player (son nickname sera égal au pseudo de l'user, l'email sera pseudo@yopmail), relier ce player au user, et enregistrez les modifications en bdd
    Faites une redirection vers la page liste des users avec un message d'alerte succès : Mise à jour réussie.
    (pour la route : en php 7, rajouter  : requirements={"id"="[0-9]+"}, cette notation c'est pour l'id cf au-dessus)
    */
    #[Route('/update', name:'app_admin_user_update')]
    public function userUpdate(UserRepository $ur, EntityManagerInterface $em)
    {
        //on récupère tous les users (grace à userRepository et la ligne qui suit)
        $listeUser = $ur->findAll();
        $compteur =0;
        foreach ($listeUser as $utilisateur) {
            if(!in_array("ROLE_ADMIN", $utilisateur->getRoles() ) && !$utilisateur->getPlayer() ){ 
                    $nouveauJoueur = new Player;
                    $nouveauJoueur->setNickname( $utilisateur->getPseudo() )
                                ->setEmail($utilisateur->getPseudo().'@yopmail.com');
                    //on va relier l'utilisateur avec le nouveauplayer:
                    $utilisateur->setPlayer($nouveauJoueur);
                    $compteur++;
            }
    }
        $em->flush();
        //pour affichez le msg flash (addflash)
        $this->addFlash('success',"Mise à jour des utilisateurs réussie! $compteur joueurs créés");

        return $this->redirectToRoute('app_admin_player_index');
    }

}
