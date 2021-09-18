<?php

namespace App\Controller;

use App\Services\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/account/address", name="back_account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }

    /**
     * @Route("/account/add-address", name="back_account_add_address")
     */
    public function add(Cart $cart, Request $request): Response
    {
        $address = new Address();
        $addressForm = $this->createForm(AddressType::class, $address);

        $addressForm->handleRequest($request);
        if($addressForm->isSubmitted() && $addressForm->isValid()){
            $address->setUser($this->getUser());
            $this->entityManager->persist($address);
            $this->entityManager->flush();
            if($cart->get()){
                return $this->redirectToRoute('order'); 
            }
            return $this->redirectToRoute('back_account_address'); 
        }

        return $this->render('account/address-form.html.twig', [
            'address_form' => $addressForm->createView()
        ]);
    }

    /**
     * @Route("/account/edit-address/{id}", name="back_account_edit_address")
     */
    public function edit(Request $request, $id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('back_account_address'); 
        }

        $addressForm = $this->createForm(AddressType::class, $address);

        $addressForm->handleRequest($request);
        if($addressForm->isSubmitted() && $addressForm->isValid()){
            $this->entityManager->flush();
            return $this->redirectToRoute('back_account_address'); 
        }

        return $this->render('account/address-form.html.twig', [
            'address_form' => $addressForm->createView()
        ]);
    }

    /**
     * @Route("/account/remove-address/{id}", name="back_account_remove_address")
     */
    public function remove($id): Response
    {
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if($address || $address->getUser() == $this->getUser()){
            $this->entityManager->remove($address);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('back_account_address'); 
    }
}
