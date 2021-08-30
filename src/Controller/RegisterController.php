<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;  
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        // On crée un user
        $user = new User();
        // On crée le formulaire d'enregistrement pour notre user
        $form = $this->createForm(RegisterType::class, $user);
        // On hydrate notre formulaire avec les éventuelles données de la request
        $form->handleRequest($request);
        // On test si le formualire a été soumis et si il est valide
        if($form->isSubmitted() && $form->isValid()){
            // On récupère le données de notre formulaire
            $user = $form->getData();
            // On encode et set le password du user
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            // On persiste et flush nos données en base
            $this->entityManager->persist($user);
            $this->entityManager->flush();  
            return $this->redirectToRoute('homepage');          
        }

        return $this->render('register/index.html.twig', [
            'register_form' => $form->createView()
        ]);
    }
}
