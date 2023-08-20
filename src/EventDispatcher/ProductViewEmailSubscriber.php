<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendEmail(ProductViewEvent $productViewEvent): void
    {
        $this->logger->info('Email envoyé pour le produit n°' . $productViewEvent->viewProduct()->getId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }
}