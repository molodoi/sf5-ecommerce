<?php

namespace App\Services;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityMananger;
    
    public function __construct(SessionInterface $session, EntityManagerInterface $entityMananger)
    {
        $this->session = $session;  
        $this->entityMananger = $entityMananger;
    }

    public function add($id){

        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id] = 1;  
        }

        return $this->session->set('cart', $cart);
    }

    public function sub($id){

        $cart = $this->session->get('cart', []);

        if(isset($cart[$id]) && $cart[$id] > 1){
            $cart[$id]--;
        }else{
            unset($cart[$id]); 
        }

        return $this->session->set('cart', $cart);
    }

    public function remove($id){

        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    public function get(){
        return $this->session->get('cart');
    }

    public function clear(){
        return $this->session->remove('cart');     
    }

    public function getCartProducts(){
        $products = [];

        if($this->get()){
            foreach($this->get() as $id => $qte){
                $product = $this->entityMananger->getRepository(Product::class)->findOneById($id);

                if(!$product){
                    $this->remove($id);
                    continue;
                }

                if($qte > 0){
                    $products[] = [
                        'product' => $product,
                        'quantity' => $qte
                    ]; 
                }              
            }
        } 
        return $products;  
    }
}