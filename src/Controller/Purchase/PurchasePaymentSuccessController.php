<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PurchasePaymentSuccessController extends AbstractController
{
    #[Route('/purchase/terminate/{{id}}', name: 'purchase_payment_success')]
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour confirmer une commande')]
    public function success($id, PurchaseRepository $purchaseRepository, CartService $cartService): RedirectResponse
    {
        $purchase = $purchaseRepository->find($id);

        if (!$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)) {
            $this->addFlash('warning', 'La commande n\'existe pas ou a déjà été payée');
            return $this->redirectToRoute('cart_show');
        }

        $purchase->setStatus(Purchase::STATUS_PAID);

        $purchaseRepository->save($purchase, true);

        $cartService->empty();

        $this->addFlash('success', 'La commande a été payée et confirmée');
        return $this->redirectToRoute('purchases_index');
    }
}