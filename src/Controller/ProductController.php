<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchType;
use App\Services\Search;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    private $entityMananger;

    public function __construct(EntityManagerInterface $entityMananger)
    {
        $this->entityMananger = $entityMananger;
    }

    /**
     * @Route("/our-products", name="products-list")
     */
    public function index(Request $request): Response
    {
        $products = $this->entityMananger->getRepository(Product::class)->findAll();

        $search = new Search();
        $searchForm = $this->createForm(SearchType::class, $search);

        $searchForm->handleRequest($request);
        if($searchForm->isSubmitted() && $searchForm->isValid()){
            $products = $this->entityMananger->getRepository(Product::class)->findWithSearch($search);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product-show")
     */
    public function show($slug): Response
    {
        $product = $this->entityMananger->getRepository(Product::class)->findOneBySlug($slug);

        return $this->render('product/show.html.twig', [
            'product' => $product   
        ]);
    }
}
