<?php

namespace App\Http\Middleware;

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
            'pendingPrRequest' => ProductionRequest::select('pe_id', 'pe_num')
                ->where([['pe_generate_code', '0'], ['pe_status', '1']])
                ->get(),
            // 'barcodeScan' => function () use ($request){
            //     return [
            //         'lname' => $request->session()->get('firstname') ?? 0,
            //         'fname' => $request->session()->get('lastname') ?? 0,
            //         'mname' => $request->session()->get('middlename') ?? 0,
            //         'ext' => $request->session()->get('ext') ?? 0,
            //         'bcode' => $request->session()->get('barcode') ?? 0,
            //         'denomination' => $request->session()->get('denomination') ?? 0,
            //     ];
            // }
        ];
    }
}
