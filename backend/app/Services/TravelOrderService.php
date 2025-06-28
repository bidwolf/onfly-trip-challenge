<?php

namespace App\Services;

use App\DTO\TravelOrderDTO;
use App\Enum\TravelOrderStatus;
use App\Http\Resources\TravelOrderResource;
use App\Models\TravelOrder;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * An service that is responsible to deal with Travel Orders logic
 */
interface TravelOrderServiceInterface
{
  public function createOrder(User $user, TravelOrderDTO $order): TravelOrderResource;
}
class TravelOrderService implements TravelOrderServiceInterface
{
  public function createOrder(User $user, TravelOrderDTO $orderDTO): TravelOrderResource
  {
    if (!$user->id) {
      abort(401, 'O usuário não possui permissão para fazer essa requisição');
    }
    try {
      DB::beginTransaction();
      $data = [
        'user_id' => $user->id,
        'requester_name' => $orderDTO->requester_name,
        'destination' => $orderDTO->destination,
        'departure_date' => $orderDTO->departure_date,
        'return_date' => $orderDTO->return_date,
        'status' => TravelOrderStatus::Pending
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
}
