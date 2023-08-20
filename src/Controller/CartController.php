<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'cart_')]
class CartController extends AbstractController
{

    protected ProductRepository $productRepository;
    protected CartService $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService) {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    #[Route('/add/{id}', name: 'add', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function add($id, Request $request): Response
    {
        // 0 - Vérifier que le produit existe
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas !");
        }

        $this->cartService->add($id);

        $this->addFlash('success', 'Le produit a bien été ajouté au panier !');

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('product_show', ['category_slug' => $product->getCategory()->getSlug(), 'slug' => $product->getSlug()]);
    }

    #[Route('/', name:'show', methods: ['GET'])]
    public function show(): Response {
        $form = $this->createForm(CartConfirmationType::class);

        $detailedCart = $this->cartService->getDetailedCartItems();

        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
            'confirmationForm' => $form
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete($id): Response {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }

        $this->cartService->remove($id);

        $this->addFlash('success', 'Le produit a bien été supprimé du panier !');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/decrement/{id}', name: 'decrement', requirements: ['id' => '\d+'])]
    public function decrement($id): Response {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }

        $this->cartService->decrement($id);

        $this->addFlash('success', 'Le produit a bien été décrémenté du panier !');

        return $this->redirectToRoute('cart_show');
    }
}
