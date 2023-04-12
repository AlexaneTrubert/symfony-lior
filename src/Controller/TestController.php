<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test/{age<\d+>?0}', name: 'test')] // #[Route('/test/{age}', name: 'test', requirements: ["age" => "\d+"], defaults: ["age" => 0])]
    public function test($age)
    {
        return new Response("Vous avez $age ans !");
    }
}