<?php

namespace App\Http\Controllers\Iad\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Services\Treasury\ColumnHelper;
use Illuminate\Http\Request;

class SpecialExternalGcRequestController extends Controller
{
    public function approvedGc(Request $request)
    {

        $data = SpecialExternalGcrequest::with(
            'user:user_id,firstname,lastname',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'hasManySpecialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty'
        )
            ->select(
                'spexgc_company',
                'spexgc_reqby',
                'spexgc_num',
                'spexgc_dateneed',
                'spexgc_id',
                'spexgc_datereq',
            )
            ->where([['spexgc_status', 'approved'], ['spexgc_reviewed', '']])
            ->orderBy('spexgc_id')
            // ->get();
            ->paginate(10)
            ->withQueryString();

        // dd(SpecialExternalGcRequestResource::collection($data)->toArray($request));
        return inertia('Iad/Dashboard/ApprovedGcTable', [
            'data' => SpecialExternalGcRequestResource::collection($data),
            'columns' => ColumnHelper::$approvedGcForReviewed,
            'filters' => $request->only('search', 'date')
        ]);
    }

    public function viewApprovedGcRecord(Request $request, SpecialExternalGcrequest $id)
    {
        $record = $id->load(
            'user:user_id,firstname,lastname,usertype',
            'user.accessPage:access_no,title',
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'approvedRequest:reqap_id,reqap_trid,reqap_preparedby,reqap_date,reqap_remarks,reqap_doc,reqap_checkedby,reqap_approvedby',
            'approvedRequest.user:user_id,firstname,lastname',
            'hasManySpecialExternalGcrequestItems:specit_trid,specit_denoms,specit_qty',
            'document:doc_id,doc_trid,doc_fullpath,doc_type'
        );
        return inertia('Iad/Dashboard/ViewApprovedGcTable', [
            'data' => new SpecialExternalGcRequestResource($record),
            'title' => 'Special External Gc'
        ]);

    }

    public function barcodeSubmission(Request $request, $id)
    {
        $gc = SpecialExternalGcrequestEmpAssign::select('spexgcemp_trid', 'spexgcemp_denom', 'spexgcemp_fname','spexgcemp_lname','spexgcemp_mname','spexgcemp_extname','spexgcemp_barcode','spexgcemp_review','spexgcemp_id')
        ->where([
            ['spexgcemp_trid', $id],
            ['spexgcemp_review', ''],
            ['spexgcemp_barcode', $request->barcode]
        ])
            ->withWhereHas('specialExternalGcrequest', function ($q){
                $q->where('spexgc_status','approved');
            })->get();

        if($gc->isEmpty()){
            return redirect()->back()->with('error', "GC Barcode # {$request->barcode} not Found!");
        }
        
        if(!empty($gc->spexgcemp_review)){
            return redirect()->back()->with('error', "GC Barcode # {$request->barcode} already Reviewed!");
        }

        if($gc->spexgc_status!='approved'){
            return redirect()->back()->with('error', "GC Barcode # {$request->barcode} GC request is still Pending!");
        }
        
        
    }
}
