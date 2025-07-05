<?php

use App\Models\TravelOrder;
use Illuminate\Support\Facades\Broadcast;
use App\Models\User;


Broadcast::channel('order.approved.{travel_order_id}', function (User $user, $travel_order_id) {
    $travel_order = TravelOrder::find($travel_order_id);
    return $travel_order && $user->id === $travel_order->user_id;
}, ['guards' => ['api']]);

Broadcast::channel('order.cancelled.{travel_order_id}', function (User $user, $travel_order_id) {
    $travel_order = TravelOrder::find($travel_order_id);
    return $travel_order && $user->id === $travel_order->user_id;
}, ['guards' => ['api']]);
Broadcast::channel('App.Models.User.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
}, ['guards' => ['api']]);
