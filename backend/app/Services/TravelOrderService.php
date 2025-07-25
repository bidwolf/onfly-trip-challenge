<?php

namespace App\Services;

use App\DTO\TravelOrderDTO;
use App\Enum\TravelOrderStatus;
use App\Notifications\TravelOrderApprovedNotification;
use App\Notifications\TravelOrderCancelledNotification;
use App\Http\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * An service that is responsible to deal with Travel Orders logic
 */
interface TravelOrderServiceInterface
{
  /**
   * Creates a new travel order
   * @param User $user The user responsible for the travel creation
   * @param TravelOrderDTO $order The order data that is going to be created
   * @return TraveLOrderResource The formatted data
   */
  public function createOrder(User $user, TravelOrderDTO $order): TravelOrderResource;
  /**
   * Lists all orders related to the user
   * @param User $user the user that is going to list orders
   * @param ?TravelOrderStatus $status the status filter
   * @param ?string $destination the destination filter
   * @param ?Carbon $start_date the start_date filter
   * @param ?Carbon $end_date the end_date filter
   * @return ResourceCollection The list of orders formatted
   */
  public function listOrders(
    User $user,
    ?TravelOrderStatus $status,
    ?string $destination,
    ?Carbon $start_date,
    ?Carbon $end_date,
  ): ResourceCollection;
  /**
   * Cancel the current travel order
   * @param TravelOrder $order the order that is going to be cancelled 
   */
  public function cancelOrder(
    TravelOrder $order
  ): TravelOrderResource;
  /**
   * Aproves the current travel order
   * @param TravelOrder $order the order that is going to be approved 
   */
  public function approveOrder(
    TravelOrder $order
  ): TravelOrderResource;
  /**
   * Update the current travel order 
   * @param array $data the order data to be updated
   * @param TravelOrder $order the order that is going to be updated
   */
  public function updateOrder(
    array $data,
    TravelOrder $order,
  ): TravelOrderResource;
}
class TravelOrderService implements TravelOrderServiceInterface
{
  public function createOrder(User $user, TravelOrderDTO $orderDTO): TravelOrderResource
  {
    try {
      DB::beginTransaction();
      $data = [
        'user_id' => $user->id,
        'requester_name' => $orderDTO->requester_name,
        'destination' => $orderDTO->destination,
        'departure_date' => $orderDTO->departure_date,
        'return_date' => $orderDTO->return_date,
        'status' => TravelOrderStatus::Pending,
        'price' => $orderDTO->price,
        'hosting' => $orderDTO->hosting,
        'transportation' => $orderDTO->transportation,
        'description' => $orderDTO->description,
      ];
      $created_order = TravelOrder::create($data);
      DB::commit();
      return (new TravelOrderResource($created_order))
        ->additional([
          'message' => 'Pedido de viagem criado com sucesso'
        ]);
    } catch (QueryException $e) {
      DB::rollBack();
      abort(500, 'Ocorreu um erro ao salvar seu pedido. Por favor tente novamente mais tarde.' . $e->getMessage());
    } catch (\Exception $e) {
      DB::rollBack();
      abort(500, 'Um erro inesperado ocorreu durante sua solicitação. Por favor tente novamente mais tarde.');
    }
  }
  public function listOrders(User $user, ?TravelOrderStatus $status, ?string $destination, ?Carbon $start_date, ?Carbon $end_date): ResourceCollection
  {
    $query = TravelOrder::query()
      ->when($status, function (Builder $query) use ($status) {
        $query->where('status', $status->value);
      })
      ->when($destination, function (Builder $query) use ($destination) {
        $query->where('destination', 'like', "%{$destination}%");
      })
      ->when($start_date, function (Builder $query) use ($start_date) {
        $query->where('departure_date', '>=', $start_date->format('Y-m-d'));
      })
      ->when($end_date, function (Builder $query) use ($end_date) {
        $query->where('return_date', '<=', $end_date->format('Y-m-d'));
      })
      ->when(!$user->is_admin, function (Builder $query) use ($user) {
        $query->where('user_id', $user->id);
      });

    return $query->get()->toResourceCollection();
  }
  public function cancelOrder(TravelOrder $order): TravelOrderResource
  {
    try {

      DB::beginTransaction();
      $order->update([
        'status' => TravelOrderStatus::Cancelled->value
      ]);
      DB::commit();
      $order->user->notify(new TravelOrderCancelledNotification($order->fresh())->afterCommit());
      return $order->fresh()->toResource()->additional(
        ['message' => 'Pedido cancelado com sucesso.']
      );
    } catch (\Error $e) {
      DB::rollBack();
      Log::error('Ocorreu um erro durante o cancelamento do pedido', ['erro' => $e->getMessage()]);
      abort(500, 'Um erro inesperado ocorreu ao tentar cancelar este pedido. Por favor tente novamente mais tarde.');
    }
  }
  public function approveOrder(TravelOrder $order): TravelOrderResource
  {
    try {
      DB::beginTransaction();
      $order->update([
        'status' => TravelOrderStatus::Approved->value
      ]);
      DB::commit();
      $order->user->notify(new TravelOrderApprovedNotification($order->fresh())->afterCommit());
      return $order->fresh()->toResource()->additional(
        ['message' => 'Pedido aprovado com sucesso.']
      );
    } catch (\Error $e) {
      DB::rollBack();
      abort(500, 'Um erro inesperado ocorreu ao tentar aprovar este pedido. Por favor tente novamente mais tarde.');
    }
  }
  public function updateOrder(array $data, TravelOrder $order): TravelOrderResource
  {
    try {
      DB::beginTransaction();
      $order->update($data);
      DB::commit();
      return $order->fresh()->toResource()->additional(['message' => 'Pedido atualizado com sucesso.']);
    } catch (\Throwable $th) {
      DB::rollBack();
      abort(500, 'Um erro inesperado ocorreu ao tentar atualizar este pedido. Por favor tente novamente mais tarde.');
    }
  }
}
