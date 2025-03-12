<?php

namespace App\Traits;

use App\Models\User;
use App\Models\DtiBarcodes;
use App\Models\DtiDocument;
use App\Models\DtiGcRequest;
use Illuminate\Http\Request;
use App\Models\DtiGcRequestItem;
use App\Models\DtiApprovedRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;

trait DtiGcTraits
{
    //
    public function getDtiPendingGcRequest()
    {
        $data = DtiGcRequest::select('dti_num', 'dti_datereq', 'dti_dateneed', 'firstname', 'lastname', 'spcus_companyname', )
            ->join('users', 'user_id', 'dti_reqby')
            ->join('special_external_customer', 'spcus_id', 'dti_company')
            ->where('dti_status', 'pending')
            ->where('dti_addemp', 'pending')
            ->get();

        $data->transform(function ($item) {
            $dtiItems = DtiGcRequestItem::where('dti_trid', $item->dti_num)->get();

            $dtiItems->each(function ($item) {

                $item->subtotal = $item->dti_denoms * $item->dti_qty;

                return $item;
            });

            $item->total += $dtiItems->sum('subtotal');
            $item->dateNeed = Date::parse($item->dti_dateneed)->toFormattedDateString();
            $item->dateReq = Date::parse($item->dti_datereq)->toFormattedDateString();

            return $item;
        });

        return $data;
    }

    public function dtiApprovedRequestView(Request $request)
    {
        return DtiGcRequest::where([['dti_status', 'approved'], ['dti_addemp', 'done']])
            ->whereAny([
                'dti_num',
                'dti_datereq',
                'dti_dateneed',
                'dti_customer',
                'dti_approveddate',
                'dti_approvedby',
            ], 'like', '%' . $request->search . '%')
            ->orderByDesc('dti_num')
            ->paginate(10);
    }

    public function dtiApprovedViewList(Request $request)
    {
        // dd($request->id);
        $data = DtiApprovedRequest::where(
            [
                ['dti_approved_requests.dti_approvedtype', 'Special External GC Approved'],
                ['dti_approved_requests.dti_trid', $request->id]
            ]
        )
            ->join('dti_gc_requests', 'dti_gc_requests.dti_num', '=', 'dti_approved_requests.dti_trid')
            ->select(
                'dti_gc_requests.dti_remarks as firstRemarks',
                'dti_gc_requests.dti_reqby',
                'dti_datereq',
                'dti_paymenttype',
                'dti_dateneed',
                'dti_reqby',
                'dti_trid',
                'dti_gc_requests.dti_approveddate',
                'dti_gc_requests.dti_approvedby',
                'dti_approved_requests.dti_checkby',
                'dti_approved_requests.dti_remarks',
                'dti_approved_requests.dti_preparedby',
            )
            ->paginate(10);
        $data->transform(function ($item) {
            $item->user = User::where('users.user_id', $item->dti_reqby)->first();
            $item->dti_reqby = ucwords($item->user->firstname . ' ' . $item->user->lastname);

            $item->prepared_by = User::where('users.user_id', operator: $item->dti_preparedby)->first();
            $item->dti_preparedby = ucwords($item->prepared_by->firstname . ' ' . $item->prepared_by->lastname);

            $item->amount = DtiGcRequestItem::where('dti_trid', $item->dti_trid)
                ->sum(DB::raw('dti_denoms * dti_qty'));

            $item->doc = DtiDocument::where('dti_documents.dti_trid', $item->dti_trid)->first();
            $item->dti_doc = $item->doc->dti_fullpath;

            $item->document = DtiDocument::where('dti_trid', $item->dti_trid)->first();
            $item->approved_doc = $item->document->dti_fullpath;
            return $item;
        });

        $barcode = DtiBarcodes::where('dti_trid', $request->id)->paginate(10);
        return [
            'data' => $data,
            'barcode' => $barcode
        ];
    }
}

