<?php

namespace App\Taxes;

use Psr\Log\LoggerInterface;

class Calculator
{
    protected LoggerInterface $logger;
    protected float $tva;

    public function __construct(LoggerInterface $logger, $tva)
    {
        $this->logger = $logger;
        $this->tva = $tva;
    }
    public function calcul(float $prix): float
    {
        $this->logger->info("Un calcul a lieu : $prix");
        return $prix * (20 / 100);
    }
}
