<?php

namespace App\Http\Controllers;

use App\DTO\TravelOrderDTO;
use App\Enum\TravelOrderStatus;
use App\Http\Requests\StoreTravelOrderRequest;
use App\Models\TravelOrder;
use App\Services\TravelOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class TravelOrderController extends Controller
{
    public function __construct(private TravelOrderService $service)
    {
        //
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $request = request();
        $filters = $request->validate([
            'status'      => ['nullable|string'],
            'destination' => 'nullable|string',
            'start_date'  => 'nullable|date|date_format:Y-m-d',
            'end_date'    => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        $status      = array_key_exists('status', $filters) ? TravelOrderStatus::tryFrom($filters['status']) : null;
        $destination = $filters['destination'] ?? null;
        $start_date  = isset($filters['start_date']) ? Carbon::parse($filters['start_date']) : null;
        $end_date    = isset($filters['end_date'])   ? Carbon::parse($filters['end_date'])   : null;
        return $this->service->listOrders($user, $status, $destination, $start_date, $end_date);
        return $this->service->listOrders();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTravelOrderRequest $request)
    {

        $travel_orderDTO = TravelOrderDTO::fromArray($request->validated());
        $user = Auth::user();
        return $this->service->createOrder($user, $travel_orderDTO);
    }

    /**
     * Display the specified resource.
     */
    public function show(TravelOrder $travel_order)
    {
        try {
            if (Auth::user()->cannot('view', $travel_order) || !$travel_order) {
                throw new NotFoundHttpException('Não foi possível encontrar o pedido solicitado');
            }
            if (Auth::user()->is_admin) {

                return response()->json(
                    [
                        'data' => [
                            'user' => $travel_order->user,
                            ...$travel_order
                                ->toResource()
                                ->toArray(request()),
                        ],
                    ]
                );
            }
            return $travel_order->toResource();
        } catch (\Error $e) {
            Log::error('Erro ao buscar pedido', ['erro' => $e->getMessage()]);
            return response()->json(
                [
                    'message' => 'Um erro inesperado ocorreu, tente novamente mais tarde.'
                ],
                500
            );
        }
    }
    /**
     * Method to approve the current order
     */
    public function approve(TravelOrder $travel_order)
    {
        Gate::authorize('approve-travel-order', $travel_order);
        return $this->service->approveOrder($travel_order);
    }
    /**
     * Method to cancel the current order
     */
    public function cancel(TravelOrder $travel_order)
    {
        Gate::authorize('cancel-travel-order', $travel_order);
        return $this->service->cancelOrder($travel_order);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TravelOrder $travel_order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TravelOrder $travel_order)
    {
        //
    }
}
