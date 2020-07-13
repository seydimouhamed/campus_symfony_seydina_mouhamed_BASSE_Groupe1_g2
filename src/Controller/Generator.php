<?php


namespace App\Controller;

use App\Repository\ChambreRepository;

class Generator
{
  
    
    public function generateNumeroChambre($idLast,$numbatimat)
    {
        //$chambre->setNumero(sprintf("%'.03d-", rand(0,999)).$form->get('numero_batimat')->getData());
        
        $idFormat=str_pad($idLast, 3, '0', STR_PAD_LEFT);
        $idBatFormat=str_pad($numbatimat, 3, '0', STR_PAD_LEFT);

        return $idFormat."-".$idBatFormat;
    }  
    public function generateMatricule($prenom,$nom)
    {
        return strtoupper(rand(2010,2017)."".substr($nom,0,2)."".substr($prenom,-2).sprintf("%'.04d", rand(0,9999)));
    }
}