<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CurrencyDetectionService;

class CurrencyDetectionController extends Controller
{
    protected $detectionService;

    public function __construct(CurrencyDetectionService $detectionService)
    {
        $this->detectionService = $detectionService;
    }

    /**
     * Show the detection form.
     */
    public function index()
    {
        return view('landing');
    }

    /**
     * Handle the image upload and prediction.
     */
    public function predict(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        $mode = $request->input('mode', 'multi');
        $result = $this->detectionService->predict($request->file('image'), $mode);

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return view('landing', ['result' => $result]);
    }
}
