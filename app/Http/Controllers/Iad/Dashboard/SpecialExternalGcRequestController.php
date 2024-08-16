<?php

namespace App\Http\Controllers\Iad\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\SpecialExternalGcrequest;
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
            // 'hasManySpecialExternalGcrequestItems:spcus_id,spcus_acctname,spcus_companyname'
        );

        // dd($record);
        return inertia('Iad/Dashboard/ViewApprovedGcTable',[
            'data' => new SpecialExternalGcRequestResource($record),

        ]);
        // special_external_gcrequest.spexgc_num,
        // special_external_gcrequest.spexgc_dateneed,
        // special_external_gcrequest.spexgc_id,
        // special_external_gcrequest.spexgc_datereq,
        // CONCAT(users.firstname,' ',users.lastname) as prep,
        // CONCAT(approvedprep.firstname,' ',approvedprep.lastname) as apprep,
        // special_external_customer.spcus_companyname,
        // special_external_customer.spcus_acctname,
        // special_external_gcrequest.spexgc_remarks,
        // special_external_gcrequest.spexgc_payment,
        // special_external_gcrequest.spexgc_paymentype,
        // special_external_gcrequest.spexgc_payment_arnum,
        // access_page.title,
        // approved_request.reqap_date,
        // approved_request.reqap_remarks,
        // approved_request.reqap_doc,
        // approved_request.reqap_checkedby,
        // approved_request.reqap_approvedby";
      
    }
}
