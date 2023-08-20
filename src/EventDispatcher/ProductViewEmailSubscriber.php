<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;
    protected MailerInterface $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public function sendEmail(ProductViewEvent $productViewEvent): void
    {
/*        $email = new TemplatedEmail();
        $email->from(new Address("contact@mail.com", "Infos pour la boutique"))
            ->to("admin@admin.fr")
            ->htmlTemplate("emails/product_view.html.twig")
            ->context([
                "product" => $productViewEvent->viewProduct(),
            ])
            ->subject("Visite du produit n°" . $productViewEvent->viewProduct()->getId());

        $this->mailer->send($email);*/
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }
}