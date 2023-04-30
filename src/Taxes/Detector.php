<?php

namespace App\Taxes;

class Detector
{
    protected float $seuil;

    public function __construct($seuil)
    {
        $this->seuil = $seuil;
    }

    public function detect(float $montant):bool
    {
        if ($montant <= $this->seuil) {
            return false;
        }
        return true;
    }

}
