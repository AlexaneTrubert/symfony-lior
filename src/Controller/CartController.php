<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{
    #[Route('/add/{id}', name: 'add', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function add($id, ProductRepository $productRepository, SessionInterface $session): Response
    {
        // 0 - Vérifier que le produit existe
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas !");
        }

        // 1 - Récupérer le panier de l'utilisateur
        // 2 - Si le panier n'existe pas, le créer avec un tableau vide
        $cart = $session->get('cart', []);

        // 3 - Voir si le produit est déjà dans le panier
        // 4 - Si le produit est déjà dans le panier, augmenter la quantité
        // 5 - Si le produit n'est pas dans le panier, l'ajouter avec une quantité de 1
        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // 6 - Sauvegarder le panier dans la session
        $session->set('cart', $cart);

        $this->addFlash('success', 'Le produit a bien été ajouté au panier !');

        return $this->redirectToRoute('product_show', ['category_slug' => $product->getCategory()->getSlug(), 'slug' => $product->getSlug()]);
    }

    #[Route('/', name:'show', methods: ['GET'])]
    public function show(ProductRepository $productRepository, SessionInterface $session): Response {
        $detailedCart = [];
        $total = 0;

        foreach ($session->get('cart', []) as $id => $quantity) {
            $product = $productRepository->find($id);
            $detailedCart[] = [
                'product' => $product,
                'qty' => $quantity
            ];

            $total += ($product->getPrice() * $quantity);
        }

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total
        ]);
    }
}
