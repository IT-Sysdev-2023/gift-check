<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use Illuminate\Http\Request;

class SpecialGcRequestController extends Controller
{
    public function pendingSpecialGc(Request $request)
    {
        $record = SpecialExternalGcrequest::with(
            'user:user_id,firstname,lastname',
            'specialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname'
        )
            ->select('spexgc_num', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq', 'spexgc_company', 'spexgc_reqby')
            ->where([
                ['special_external_gcrequest.spexgc_status', 'pending'],
                ['special_external_gcrequest.spexgc_promo', '0']
            ])
            ->paginate()
            ->withQueryString();

        // dd(SpecialExternalGcRequestResource::collection($record)->toArray(request()));

        return inertia(
            'Treasury/Dashboard/SpecialGcTable',
            [
                'filters' => $request->only('search', 'date'),
                'title' => 'Special GC Request',
                'data' => SpecialExternalGcRequestResource::collection($record),
                'columns' => ColumnHelper::$pendingSpecialGc,
            ]
        );
    }
    public function updatePendingSpecialGc(SpecialExternalGcrequest $id)
    {
        $record = $id->load('specialExternalCustomer', 'specialExternalBankPaymentInfo', 'document', 'specialExternalGcrequestEmpAssign');

        // dd($record->toArray());
        // dd((new SpecialExternalGcRequestResource($record))->toArray(request()));
        return inertia(
            'Treasury/Dashboard/UpdateSpecialExternal',
            [
                'title' => 'Special GC Request',
                'data' => new SpecialExternalGcRequestResource($record),
                'options' => self::options()
            ]
        );
    }

    private function options()
    {
        return SpecialExternalCustomer::has('user')
            ->select('spcus_id as value', 'spcus_by', 'spcus_companyname as label', 'spcus_acctname as account_name')
            ->where('spcus_type', 2)
            ->orderByDesc('spcus_id')
            ->get();
    }

    public function getAssignEmployee(Request $request)
    {
        //in Development
        $record = SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_fname as fname',
            'spexgcemp_lname as lname',
            'spexgcemp_mname as mname',
            'spexgcemp_extname as xname'
        )->where('spexgcemp_trid', $request->id)->get();

        dd($request->id);
        return response()->json([
            'data' => $record,
            'columns' => [
                [
                    'title' => 'Last Name',
                    'dataIndex' => 'lname',
                ],
                [
                    'title' => 'First Name',
                    'dataIndex' => 'fname',
                ],
                [
                    'title' => 'Middle Name',
                    'dataIndex' => 'mname',
                ],
                [
                    'title' => 'Name Ext.',
                    'dataIndex' => 'xname',
                ],
            ]
        ]);
    }

    public function addAssignEmployee(Request $request)
    {
        dd($request->all());
    }
}
