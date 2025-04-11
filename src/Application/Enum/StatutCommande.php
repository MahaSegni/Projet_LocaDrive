<?php
namespace App\Application\Enum;

enum StatutCommande: string
{
    case CART = 'panier';
    case EN_ATTENTE = 'en_attente';
    case VALIDEE = 'validee';
    case ANNULEE='annulee';
}