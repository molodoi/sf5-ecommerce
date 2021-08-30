<?php

namespace App\Controller;

use App\Form\UpdatePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;  
    }

    /**
     * @Route("/account", name="back_account")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * @Route("/account/update-password", name="back_account_update_password")
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        // On initialise un variable de notification
        $notif_message = null;

        // On récupère notre user
        $user = $this->getUser();

        // On crée le formulaire de maj du mot de passe
        $update_password_form = $this->createForm(UpdatePasswordType::class, $this->getUser());

        // On hydrate notre formulaire avec les éventuelles données de la request
        $update_password_form->handleRequest($request);

        // On test si le formualire a été soumis et si il est valide
        if($update_password_form->isSubmitted() && $update_password_form->isValid()){
            // On test si l'email a changé
            if($update_password_form->get('email')->getData() != $user->getEmail()){
                $user->setEmail($update_password_form->get('email')->getData());   
            }
            // On test si le password actuel (son ancien mot de passe) est bien celui de notre utilisateur
            if(($update_password_form->get('old_password')->getData() != null && $update_password_form->get('new_password')->getData() != null) && $encoder->isPasswordValid($user, $update_password_form->get('old_password')->getData())){
                // Si oui, confiant nous pouvons sauvegarder le nouveau mot de passe
                $user->setPassword($encoder->encodePassword($user, $update_password_form->get('new_password')->getData()));                   
                $notif_message = 'Votre mot de passe à bien été mis à jour';
            } 
            $this->entityManager->flush();                   
        }

        return $this->render('account/update-password.html.twig', [
            'update_password_form' => $update_password_form->createView(),
            'notif_message' => $notif_message
        ]);
    }
}
