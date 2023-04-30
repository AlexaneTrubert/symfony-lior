<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], [], 3);

        return $this->render('index.html.twig', [
            'products' => $products,
        ]);
    }
}