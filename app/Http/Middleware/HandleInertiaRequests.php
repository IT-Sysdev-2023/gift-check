<?php

namespace App\Http\Middleware;

use App\Helpers\ArrayHelper;
use App\Models\ProductionRequest;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'status' => $request->session()->get('status'),
                    'error' => $request->session()->get('error'),
                    'msg' => $request->session()->get('msg'),
                    'title' => $request->session()->get('title'),
                    'type' => $request->session()->get('type'),
                    'description' => $request->session()->get('description'),
                    'data' => $request->session()->get('data') ?? [],

                    //San Changes
                    'stream' => $request->session()->get('stream'),
                    'countSession' => $request->session()->get('countSession') ?? 0,
                    'denominationSession' => $request->session()->get('denominationSession') ?? 0,
                    'scanGc' => $request->session()->get('scanGc') ?? [],
                ];
            },
            'pendingPrRequest' => $request->user() ? ProductionRequest::select('pe_id', 'pe_num')
                ->where([['pe_generate_code', '0'], ['pe_status', '1']])
                ->get() : [],
            'barcodeReviewScan' => function () use ($request) {
                return [
                    'allocation' => $request->session()->get('scanReviewGC') ?? [],
                    'promo' => $request->session()->get('scannedPromo') ?? [],
                ];
            },
        ];
    }
}
