<?php

namespace App\Http\Controllers;

use App\Models\Gc;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    //

    public function index(Request $request)
    {

        //Treasury & Marketing
        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'Marketing',
                'status' => 'finish',
                'description' => 'Request Approved'
            ]
        ]);

        //if Scanned By Fad
        $step3 = Gc::whereHas('barcode', function (Builder $query) use ($request) {
            $query->where('barcode_no', $request->barcode);
        })->exists();
        
        if ($step3) {
            $steps->push((object) [
                'title' => 'FAD',
                'description' => 'Scanned By FAD'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'FAD',
                'description' => 'Not Scanned By FAD'
            ]);
        }


        return Inertia::render('Admin/AdminDashboard', ['data' => $steps, 'latestStatus' => 0]);
    }
    public function scanGcStatusIndex()
    {
        return Inertia::render('Admin/ScanGcStatuses');
    }
    public function barcodeStatus()
    {

    }

}
