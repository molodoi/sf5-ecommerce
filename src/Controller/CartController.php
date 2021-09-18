<?php

namespace App\Controller;

use App\Entity\Product;
use App\Services\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/cart", name="cart")
     */
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig',[
            'products' => $cart->getCartProducts()
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    public function add(Cart $cart, $id)
    {
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/sub/{id}", name="sub_to_cart")
     */
    public function sub(Cart $cart, $id)
    {
        $cart->sub($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/remove/{id}", name="remove_to_cart")
     */
    public function remove(Cart $cart, $id)
    {
        $cart->remove($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/clear", name="clear_cart")
     */
    public function clear(Cart $cart)
    {
        $cart->clear();

        return $this->redirectToRoute('products-list');
    }
}
