<?php

namespace App\Enum;

enum TravelOrderStatus: string
{
  case Cancelled = 'cancelado';
  case Approved = 'aprovado';
  case Pending = 'pendente';
}
