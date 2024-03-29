<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\Cart;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Services\Mailjet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;  
    }

    /**
     * @Route("/order", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    {
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('back_account_add_address');
        }

        $form_order = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form_order' => $form_order->createView(),
            'products' => $cart->getCartProducts()
        ]);
    }


    /**
     * @Route("/order/summary", name="order_summary", methods="POST")
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form_order = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form_order->handleRequest($request);
        if($form_order->isSubmitted() && $form_order->isValid()){
            $date = new \DateTime();
            $carriers = $form_order->get('carriers')->getData();

            $delivery = $form_order->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br />'.$delivery->getPhone();

            if($delivery->getCompany()){
                $delivery_content .= '<br />'.$delivery->getCompany();
            }

            $delivery_content .= '<br />'.$delivery->getAddress();
            $delivery_content .= '<br />'.$delivery->getZipcode().' '.$delivery->getCity();
            $delivery_content .= '<br />'.$delivery->getCountry();
            
            // Enregistrer la commande
            $order = new Order();
            // On crée une référence de commande
            $reference = $date->format('dmY-his').'-'.random_int(24, 998);
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);

            $this->entityManager->persist($order);

            $products_for_stripe = [];
            $my_domain = 'http://sf5-ecommerce.test';
            // Enregistrer les produits            
            foreach($cart->getCartProducts() as $product){
                $order_details = new OrderDetails();
                $order_details->setMyOrder($order);
                $order_details->setProduct($product['product']->getName());
                $order_details->setQuantity($product['quantity']);
                $order_details->setPrice($product['product']->getPrice());
                $order_details->setTotal($product['product']->getPrice() * $product['quantity']);

                $this->entityManager->persist($order_details);
            }

            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [
                'products' => $cart->getCartProducts(),
                'carrier' => $carriers,
                'delivery' => $delivery,
                'reference' => $order->getReference()
            ]);
        }

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/order/thanks/{stripe_session_id}", name="order_success")
     */
    public function success(Cart $cart, Mailjet $mail, $stripe_session_id)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripe_session_id);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        if ($order->getState() == 0) {
            // Vider la session "cart"
            $cart->clear();

            // Modifier le statut de notre commande en mettant 1 Payé
            $order->setState(1);
            $this->entityManager->flush();

            // Envoyer un email à notre client pour lui confirmer sa commande
            $content = "Bonjour ".$order->getUser()->getFirstname()."<br/>Merci pour votre commande.<br><br/>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam expedita fugiat ipsa magnam mollitia optio voluptas! Alias, aliquid dicta ducimus exercitationem facilis, incidunt magni, minus natus nihil odio quos sunt?";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande est bien validée.', 'Merci', $content);

        }

        return $this->render('order/success.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/order/error/{stripe_session_id}", name="order_cancel")
     */
    public function error(Mailjet $mail, $stripe_session_id)
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripe_session_id);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // Envoyer un email à notre utilisateur pour lui indiquer l'échec de paiement
        $content = "Bonjour ".$order->getUser()->getFirstname()."<br/>Oups! votre commande n'a pas été validé.<br><br/>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam expedita fugiat ipsa magnam mollitia optio voluptas! Alias, aliquid dicta ducimus exercitationem facilis, incidunt magni, minus natus nihil odio quos sunt?";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Oups! votre commande n\'a pas été validé.', 'Oups!', $content);


        return $this->render('order/cancel.html.twig', [
            'order' => $order
        ]);
    }


}
