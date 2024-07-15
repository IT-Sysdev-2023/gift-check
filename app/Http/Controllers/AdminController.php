<?php

namespace App\Http\Controllers;

use App\Models\Gc;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    //

    public function index(Request $request)
    {
        // $regularGcStatus = self::regularGc($request);
        $regular = Gc::whereHas('barcode', function (Builder $query) use ($request) {
            $query->where('barcode_no', $request->barcode);
        })->exists();

        $special = SpecialExternalGcrequestEmpAssign::where('spexgcemp_barcode', $request->barcode)->exists();

        if($regular){
            $steps = self::regularGc($regular);
        }elseif($special){
            $steps = self::specialStatus($special);
        }


        // $specialGcStatus = ;



        return Inertia::render('Admin/AdminDashboard', ['data' => $steps, 'latestStatus' => 0]);
    }
    public function scanGcStatusIndex()
    {
        return Inertia::render('Admin/ScanGcStatuses');
    }
    public function barcodeStatus()
    {
    }

    public static function regularGc(array $step3)
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
        return $steps;
    }

    public static function specialStatus(array $special)
    {
        //Treasury & Marketing
        $steps = collect([
            [
                'title' => 'Treasury',
                'status' => 'finish',
                'description' => 'Request Submitted'
            ],
            [
                'title' => 'FAD',
                'status' => 'finish',
                'description' => 'Request Approved'
            ],
            [
                'title' => 'Finance',
                'status' => 'finish',
                'description' => 'Generated Barcode Success'
            ]
        ]);

        if ($special) {
            $steps->push((object) [
                'title' => 'FAD',
                'description' => 'Print Barcode'
            ]);
        } else {
            $steps->push((object) [
                'status' => 'error',
                'title' => 'FAD',
                'description' => 'Not Scanned By FAD'
            ]);
        }
        return $steps;
    }
}
