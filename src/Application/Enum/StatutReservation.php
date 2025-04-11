<?php

namespace App\Application\Enum;

enum StatutReservation: string
{
    case EN_ATTENTE = 'en_attente';
    case CART = 'cart';
    case ANNULEE = 'annulee';
}