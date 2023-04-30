<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    protected Calculator $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }

    #[Route('/hello/{name}',
        name: 'hello',
        requirements: ["name" => "([a-zA-Z])\w+\w+"],
        defaults: ["name" => "World"])]
    public function hello($name): Response
    {
        $ages = [12, 18, 29, 15];
        return $this->render('hello/index.html.twig', [
            'name' => $name,
            'ages' => $ages,
        ]);
    }
}
