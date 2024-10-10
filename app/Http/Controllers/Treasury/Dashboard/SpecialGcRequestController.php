<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcrequestEmpAssignResource;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Models\ApprovedRequest;
use App\Models\Assignatory;
use App\Models\LedgerBudget;
use App\Models\LedgerSpgc;
use App\Models\SpecialExternalBankPaymentInfo;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Rules\DenomQty;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Transactions\SpecialGcPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SpecialGcRequestController extends Controller
{

    public function __construct(public SpecialGcPaymentService $specialGcPaymentService)
    {

    }
    public function pendingSpecialGc(Request $request)
    {
        $externalData = $this->specialGcPaymentService->pending();

        $internalData = SpecialExternalGcrequest::with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
            ->select('spexgc_num', 'spexgc_reqby', 'spexgc_company', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->where([['special_external_gcrequest.spexgc_status', 'pending'], ['special_external_gcrequest.spexgc_promo', '*']])
            ->paginate()->withQueryString();

        return inertia(
            'Treasury/Dashboard/SpecialGcTable',
            [
                'filters' => $request->only('search', 'date'),
                'title' => 'Special GC Request',
                'externalData' => SpecialExternalGcRequestResource::collection($externalData),
                'internalData' => SpecialExternalGcRequestResource::collection($internalData),
                'columns' => ColumnHelper::$pendingSpecialGc,
            ]
        );
    }
    public function updatePendingSpecialGc(SpecialExternalGcrequest $id)
    {
        $record = $id->load([
            'specialExternalCustomer',
            'specialExternalBankPaymentInfo',
            'specialExternalGcrequestItems',
            'document',
            'specialExternalGcrequestEmpAssign'
        ]);
        // dd(vars: $record->specialExternalGcrequestEmpAssign->groupBy('spexgcemp_denom'));
        return inertia(
            'Treasury/Dashboard/UpdateSpecialExternal',
            [
                'title' => 'Special GC Request',
                'data' => new SpecialExternalGcRequestResource($record),
                'options' => self::options()
            ]
        );
    }

    public function updateSpecialGc(Request $request)
    {
        return $this->specialGcPaymentService->updateSpecial($request);

    }

    //Special Gc Payment
    public function specialGcPayment()
    {
        $transactionNumber = SpecialExternalGcrequest::max('spexgc_num');

        $transNo = $transactionNumber ?
            NumberHelper::leadingZero($transactionNumber + 1, "%03d")
            : '0001';

        return inertia('Treasury/Transactions/SpecialGcPayment/SpecialExtPayment', [
            'title' => 'Gc Payment',
            'trans' => $transNo,
            'options' => self::options()
        ]);
    }

    public function gcPaymentSubmission(Request $request)
    {

        $data = $this->specialGcPaymentService->store($request);

        $pdf = Pdf::loadView('pdf.specialexternalpayment', ['data' => $data]);

        $pdf->setPaper('A3');

        $stream = base64_encode($pdf->output());

        return redirect()->back()->with(['stream' => $stream, 'success' => 'GC External Payment submission success']);
    }

    public function pendingInternalGc()
    {
        $data = SpecialExternalGcrequest::with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
            ->select('spexgc_num', 'spexgc_reqby', 'spexgc_company', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->where([['special_external_gcrequest.spexgc_status', 'pending'], ['special_external_gcrequest.spexgc_promo', '*']])
            ->paginate()->withQueryString();
    }

    public function releasingInternal(Request $request)
    {
        $record = SpecialExternalGcrequest::
            with(['specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'user:user_id,firstname,lastname', 'approvedRequestRevied.user', 'specialExternalGcrequestEmpAssign'])
            ->withWhereHas('approvedRequest', function ($q) {
                $q->select('reqap_trid', 'reqap_approvedby')->where('reqap_approvedtype', 'Special External GC Approved');
            })
            ->select('spexgc_reqby', 'spexgc_company', 'spexgc_id', 'spexgc_num', 'spexgc_dateneed', 'spexgc_id', 'spexgc_datereq')
            ->where([['spexgc_status', 'approved'], ['spexgc_reviewed', 'reviewed'], ['spexgc_released', ''], ['spexgc_promo', '*']])
            ->orderByDesc('spexgc_id')
            ->paginate()
            ->withQueryString();

        return inertia('Treasury/Dashboard/SpecialGc/ReviewedReleasingInternal', [
            'title' => 'Reviewed GC For Releasing(Internal)',
            'records' => SpecialExternalGcRequestResource::collection($record),
            'columns' => ColumnHelper::$specialInternal
        ]);
    }

    public function viewReleasingInternal(Request $request, SpecialExternalGcrequest $id)
    {
        $rec = $id->load([
            'user' => function ($q) {
                $q->select('user_id', 'firstname', 'lastname', 'usertype')->with('accessPage:access_no,title');
            },
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'specialExternalGcrequestItems',
            'specialExternalGcrequestEmpAssign',
            'approvedRequest' => function ($q) {
                $q->select('reqap_trid', 'reqap_date', 'reqap_remarks', 'reqap_doc', 'reqap_checkedby', 'reqap_approvedby', 'reqap_preparedby')->with('user:user_id,firstname,lastname')
                    ->where('reqap_approvedtype', 'Special External GC Approved');
            }
        ]);

        $checkBy = Assignatory::assignatories($request);

        return inertia('Treasury/Dashboard/SpecialGc/Components/InternalGcReleasingView', [
            'title' => 'Special Internal Gc Releasing',
            'id' => $id->spexgc_id,
            'checkBy' => $checkBy,
            'records' => new SpecialExternalGcRequestResource($rec)
        ]);
    }

    public function relasingInternalSubmission(Request $request, $id)
    {
        // dd($id);
        $request->validate([
            "checkedBy" => 'required',
            "remarks" => 'required',
            "receivedBy" => "required"
        ]);

        DB::transaction(function () use ($id, $request) {

            SpecialExternalGcrequest::where([["spexgc_id", $id], ['spexgc_released', '']])->update([
                'spexgc_released' => 'released',
                'spexgc_receviedby' => $request->receivedBy
            ]);

            $relid = ApprovedRequest::where('reqap_approvedtype', 'special external releasing')->max('reqap_trnum');

            ApprovedRequest::create([
                'reqap_trid' => $id,
                'reqap_approvedtype' => 'special external releasing',
                'reqap_remarks' => $request->remarks,
                'reqap_preparedby' => $request->user()->user_id,
                'reqap_date' => now(),
                'reqap_trnum' => $relid,
                'reqap_checkedby' => $request->checkedBy
            ]);

            $reqType = SpecialExternalGcrequest::where('spexgc_id', $id)->value('spexgc_type');

            if ($reqType == '1') {
                $r = SpecialExternalGcrequestItem::where('specit_trid', $id)->first(['specit_denoms', 'specit_qty']);
                // dd($r);
                $total = $r->specit_denoms * $r->specit_qty;
            } else {
                // $re = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $id)->get();
                // dd($re);
                $q = SpecialExternalGcrequestEmpAssign::selectRaw("IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom),0.00) as totaldenom,
					IFNULL(COUNT(special_external_gcrequest_emp_assign.spexgcemp_denom),0) as cnt")->where('spexgcemp_trid', $id)->first();
                $total = $q->totaldenom;
            }

            $l = LedgerBudget::max('bledger_id');
            $lnum = $l ? $l + 1 : 1;
            LedgerSpgc::create([
                'spgcledger_no' => $lnum,
                'spgcledger_trid' => $id,
                'spgcledger_datetime' => now(),
                'spgcledger_type' => 'RFGCSEGCREL',
                'spgcledger_debit' => $total,
            ]);

            return redirect()->back()->with('success', 'Successfully Submitted');
        });
        return redirect()->back()->with('error', 'Something went wrong');

    }

    public function viewDenomination($id)
    {
        $record = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $id)
            ->select(['spexgcemp_denom', 'spexgcemp_fname', 'spexgcemp_lname', 'spexgcemp_mname', 'spexgcemp_extname'])
            ->orderBy('spexgcemp_denom')
            ->paginate(10)
            ->withQueryString();
        return response()->json([
            'data' => SpecialExternalGcrequestEmpAssignResource::collection($record),
            'from' => $record->firstItem(),
            'to' => $record->lastItem(),
            'total' => $record->total(),
            'links' => $record->linkCollection(),
        ]);
    }

    public function releasedGc(Request $request)
    {
        $record = SpecialExternalGcrequest::
            select('spexgc_reqby', 'spexgc_company', 'spexgc_id', 'spexgc_num', 'spexgc_datereq', 'spexgc_dateneed')
            ->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', )
            ->withWhereHas('approvedRequest', function ($q) {
                $q->with('user:user_id,firstname,lastname')->select('reqap_preparedby', 'reqap_trid', 'reqap_date')->where('reqap_approvedtype', 'special external releasing');
            })->where('spexgc_released', 'released')
            ->orderByDesc('spexgc_id')
            ->paginate()->withQueryString();
        return inertia('Treasury/Dashboard/SpecialGc/SpecialReleasedGc', [
            'title' => 'Released Special External Gc',
            'filters' => $request->only(['date', 'search']),
            'data' => SpecialExternalGcRequestResource::collection($record),
            'columns' => ColumnHelper::$specialReleasedGc
        ]);
    }

    public function viewReleasedGc(Request $request, $id)
    {
        $approvedReq = SpecialExternalGcrequest::where('spexgc_id', $id)->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'specialExternalBankPaymentInfo')
            ->withWhereHas('approvedRequest', function ($q) {
                $q->with('user:user_id,firstname,lastname')->where('reqap_approvedtype', 'Special External GC Approved');
            })->first();

        $reviewed = ApprovedRequest::with('user:user_id,firstname,lastname')
            ->select('reqap_remarks', 'reqap_date', 'reqap_preparedby')
            ->where([['reqap_trid', $id], ['reqap_approvedtype', 'special external gc review']])->first();

        $released = ApprovedRequest::with('user:user_id,firstname,lastname')
            ->select('reqap_remarks', 'reqap_date', 'reqap_preparedby')
            ->where([['reqap_trid', $id], ['reqap_approvedtype', 'special external releasing']])->first();

        $barcodes = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $id)
            ->select('spexgcemp_trid', 'spexgcemp_denom', 'spexgcemp_fname', 'spexgcemp_lname', 'spexgcemp_mname', 'spexgcemp_extname', 'spexgcemp_barcode')->paginate()->withQueryString();
        return inertia('Treasury/Dashboard/SpecialGc/Components/SpecialReleasedGcViewing', [
            'record' => new SpecialExternalGcRequestResource($approvedReq),
            'reviewed' => $reviewed,
            'released' => $released,
            'barcodes' => $barcodes,
            'title' => 'Viewing Special External Gc Request'
        ]);
    }
    private function options()
    {

        return SpecialExternalCustomer::has('user')
            ->select('spcus_id as value', 'spcus_by', 'spcus_companyname as label', 'spcus_acctname as account_name')
            ->where('spcus_type', 2)
            ->orderByDesc('spcus_id')
            ->get();
    }

}
