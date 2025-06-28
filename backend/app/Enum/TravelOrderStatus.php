<?php

namespace App\Enum;

enum TravelOrderStatus: string
{
  case Cancelled = 'cancelled';
  case Approved = 'approved';
  case Pending = 'pending';
}
