<?php

namespace App\Http\Controllers;

use App\DTO\TravelOrderDTO;
use App\Http\Requests\StoreTravelOrderRequest;
use App\Models\TravelOrder;
use App\Services\TravelOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTravelOrderRequest $request)
    {

        $orderDTO = TravelOrderDTO::fromArray($request->validated());
        $user = Auth::user();
        return $this->service->createOrder($user, $orderDTO);
    }

    /**
     * Display the specified resource.
     */
    public function show(TravelOrder $travel_order)
    {
        try {
            if (!$travel_order->empty) {
                $travel_order = TravelOrder::with('user')->find(request('travel_order'));
            }
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, TravelOrder $travelOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TravelOrder $travelOrder)
    {
        //
    }
}
