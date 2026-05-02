<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExchangeRateService;

class ExchangeRateController extends Controller
{
    protected $exchangeService;

    public function __construct(ExchangeRateService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    /**
     * Handle live currency conversion request.
     */
    public function convert(Request $request)
    {
        $request->validate([
            'from' => 'required|string|size:3',
            'to' => 'required|string|size:3',
            'amount' => 'required|numeric',
        ]);

        $result = $this->exchangeService->convert(
            $request->from,
            $request->to,
            $request->amount
        );

        return response()->json($result);
    }

    /**
     * Get latest rates for the ticker.
     */
    public function getRates(Request $request)
    {
        $base = $request->query('base', 'IDR');
        $symbols = $request->query('symbols', 'SGD,MYR,THB,PHP,VND');

        $result = $this->exchangeService->getLatestRates($base, $symbols);

        return response()->json($result);
    }
}
