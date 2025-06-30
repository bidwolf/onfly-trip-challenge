<?php

use App\Models\TravelOrder;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

Broadcast::channel('order.approved.{travel_order_id}', function (User $user, TravelOrder $travel_order) {
    return $user->id === $travel_order->user_id;
}, ['api']);
Broadcast::channel('order.cancelled.{travel_order_id}', function (User $user, TravelOrder $travel_order) {
    return $user->id === $travel_order->user_id;
}, ['api']);
