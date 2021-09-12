<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{

    private $entityMananger;

    public function __construct(EntityManagerInterface $entityMananger)
    {
        $this->entityMananger = $entityMananger;
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
    public function add(Request $request): Response
    {
        $address = new Address();
        $addressForm = $this->createForm(AddressType::class, $address);

        $addressForm->handleRequest($request);
        if($addressForm->isSubmitted() && $addressForm->isValid()){
            $address->setUser($this->getUser());
            $this->entityMananger->persist($address);
            $this->entityMananger->flush();
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
        $address = $this->entityMananger->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()){
            return $this->redirectToRoute('back_account_address'); 
        }

        $addressForm = $this->createForm(AddressType::class, $address);

        $addressForm->handleRequest($request);
        if($addressForm->isSubmitted() && $addressForm->isValid()){
            $this->entityMananger->flush();
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
        $address = $this->entityMananger->getRepository(Address::class)->findOneById($id);

        if($address || $address->getUser() == $this->getUser()){
            $this->entityMananger->remove($address);
            $this->entityMananger->flush();
        }

        return $this->redirectToRoute('back_account_address'); 
    }
}
