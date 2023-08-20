<?php

namespace App\EventDispatcher;

use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        $this->logger->info("Email envoyé pour la commande n°" . $purchaseSuccessEvent->getPurchase()->getId());
    }

    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }
}