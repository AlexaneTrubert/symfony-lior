<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected SessionInterface $session;
    protected ProductRepository $productRepository;
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    protected function saveCart(array $cart): void
    {
        $session = $this->requestStack->getSession();
        $session->set('cart', $cart);
    }

    public function add(int $id): void
    {
        // 1 - Récupérer le panier de l'utilisateur
        // 2 - Si le panier n'existe pas, le créer avec un tableau vide
        $cart = $this->getCart();
        // 3 - Voir si le produit est déjà dans le panier
        // 4 - Si le produit est déjà dans le panier, augmenter la quantité
        // 5 - Si le produit n'est pas dans le panier, l'ajouter avec une quantité de 1
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }
        $cart[$id]++;

        // 6 - Sauvegarder le panier dans la session
        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $quantity) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $quantity;
        }

        return $total;

    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $quantity) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $quantity);
        }

        return $detailedCart;
    }

    public function remove(int $id): void
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id): void
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->saveCart($cart);

    }

    public function empty(): void
    {
        $this->saveCart([]);
    }
}