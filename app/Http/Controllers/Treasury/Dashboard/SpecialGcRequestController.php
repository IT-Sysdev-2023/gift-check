<?php

namespace App\Http\Controllers\Treasury\Dashboard;

use App\Helpers\NumberHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialExternalGcrequestEmpAssignResource;
use App\Http\Resources\SpecialExternalGcRequestResource;
use App\Http\Resources\StoreGcRequestResource;
use App\Models\ApprovedRequest;
use App\Models\Assignatory;
use App\Models\DtiApprovedRequest;
use App\Models\DtiBarcodes;
use App\Models\DtiGcRequest;
use App\Models\LedgerBudget;
use App\Models\LedgerSpgc;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\SpecialExternalGcrequestItem;
use App\Models\StoreGcrequest;
use App\Models\StoreRequestItem;
use App\Services\Treasury\ColumnHelper;
use App\Services\Treasury\Transactions\SpecialGcPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class SpecialGcRequestController extends Controller
{

    public function __construct(public SpecialGcPaymentService $specialGcPaymentService) {}
    public function pendingSpecialGc(Request $request)
    {
        $externalData = $this->specialGcPaymentService->pending();
        $internalData = $this->specialGcPaymentService->pendingInternal();

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
        $spgcRequest = SpecialExternalGcrequest::max('spexgc_num');
        $dtiRequest = DtiGcRequest::max('dti_num');

        if ($spgcRequest > $dtiRequest) {
            $transactionNumber = $spgcRequest;
        } else {
            $transactionNumber = $dtiRequest;
        }

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

    public function releasingGc(Request $request)
    {
        $promo = $request->has('promo') ? $request->promo : '*';

        $record = $this->specialGcPaymentService->releasingGc($promo);

        return inertia('Treasury/Dashboard/SpecialGc/ReviewedReleasing', [
            'title' => 'Reviewed GC For Releasing',
            'records' => SpecialExternalGcRequestResource::collection($record),
            'columns' => ColumnHelper::$specialInternal,
            'tab' => $promo
        ]);
    }
    public function releasingGcDti()
    {
        return inertia('Treasury/Dashboard/SpecialGc/ReviewedReleasingDti', [
            'title' => 'Reviewed GC For Releasing Dti',
            'records' => $this->specialGcPaymentService->releasingGcDti(),
        ]);
    }

    public function viewReleasing(Request $request, SpecialExternalGcrequest $id)
    {
        $rec = $id->load([
            'user' => function ($q) {
                $q->select('user_id', 'firstname', 'lastname', 'usertype')
                    ->with('accessPage:access_no,title');
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

        return inertia('Treasury/Dashboard/SpecialGc/Components/GcReleasingView', [
            'title' => 'Special Gc Releasing',
            'id' => $id->spexgc_id,
            'checkBy' => $checkBy,
            'records' => new SpecialExternalGcRequestResource($rec)
        ]);
    }
    public function viewReleasingDtiSetup(Request $request, $id)
    {
        return inertia('Treasury/Dashboard/SpecialGc/Components/GcReleasingDtiView', [
            'title' => 'Special Gc Releasing',
            'record' => $this->specialGcPaymentService->releasingDtiReviewed($id),
            'cbyoptions' => Assignatory::assignatories($request)
        ]);
    }

    public function relasingGcSubmission(Request $request, $id)
    {
        $request->validate([
            "checkedBy" => 'required',
            "remarks" => 'required',
            "receivedBy" => "required"
        ]);
        try {
            DB::transaction(function () use ($id, $request) {

                SpecialExternalGcrequest::where([["spexgc_id", $id], ['spexgc_released', '']])->update([
                    'spexgc_released' => 'released',
                    'spexgc_receviedby' => $request->receivedBy
                ]);

                $relid = ApprovedRequest::where('reqap_approvedtype', 'special external releasing')
                    ->max('reqap_trnum');

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

                    $total = $r->specit_denoms * $r->specit_qty;
                } else {
                    // $re = SpecialExternalGcrequestEmpAssign::where('spexgcemp_trid', $id)->get();

                    $q = SpecialExternalGcrequestEmpAssign::selectRaw("IFNULL(SUM(special_external_gcrequest_emp_assign.spexgcemp_denom),0.00) as totaldenom,
					IFNULL(COUNT(special_external_gcrequest_emp_assign.spexgcemp_denom),0) as cnt")->where('spexgcemp_trid', $id)->first();
                    $total = $q->totaldenom;
                }

                $l = LedgerBudget::max('bledger_id');
                $lnum = $l ? $l + 1 : 1;

                //external
                if ($request->promo === '0') {
                    LedgerBudget::create([
                        'bledger_no' => $lnum,
                        'bledger_trid' => $id,
                        'bledger_datetime' => now(),
                        'bledger_type' => 'RFGCSEGCREL',
                        'bdebit_amt' => $total,
                        'bledger_category' => 'special'
                    ]);
                } else {
                    //internal
                    LedgerSpgc::create([
                        'spgcledger_no' => $lnum,
                        'spgcledger_trid' => $id,
                        'spgcledger_datetime' => now(),
                        'spgcledger_type' => 'RFGCSEGCREL',
                        'spgcledger_debit' => $total,
                    ]);
                }
                return redirect()->back()->with('success', 'Successfully Submitted');
            });
        } catch (e) {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function releasingSubmissionDti(Request $request, $id)
    {
        $request->validate([
            "checkby" => 'required',
            "remarks" => 'required',
            "receivedby" => "required"
        ]);

        $dbtransaction = DB::transaction(function () use ($id, $request) {
            $this->specialGcPaymentService->dtiGcRequestUpdate($request, $id)
                ->dtiApprovedRequestCreate($request, $id)
                ->ledgerBudgetCreate($id);
        });

        if ($dbtransaction) {
            return redirect()->back()->with('success', 'Successfully Submitted');
        } else {
            return redirect()->back()->with('error', 'Something went wrong');
        }
    }

    public function viewDenomination($id)
    {
        // dd();
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

    public function getDtiDenomination($id)
    {
        $data = DtiBarcodes::where('dti_trid', $id)
            ->select(['dti_denom', 'fname', 'lname', 'mname', 'extname'])
            ->orderBy('dti_denom')
            ->get();

        $data->each(function ($item) {
            $item->dti_denom = NumberHelper::currency($item->dti_denom);
            $item->fname = Str::ucfirst($item->fname);
            $item->lname = Str::ucfirst($item->lname);
            $item->mname = Str::ucfirst($item->mname);
            return $item;
        });
        return response()->json([
            'records' => $data,
        ]);
    }

    public function releasedGc(Request $request)
    {
        $promo = $request->has('promo') ? $request->promo : '*';
        $record = SpecialExternalGcrequest::select('spexgc_reqby', 'spexgc_company', 'spexgc_id', 'spexgc_num', 'spexgc_datereq', 'spexgc_dateneed')
            ->where('spexgc_promo', $promo)
            ->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',)
            ->withWhereHas('approvedRequest', function ($q) {
                $q->with('user:user_id,firstname,lastname')->select('reqap_preparedby', 'reqap_trid', 'reqap_date')->where('reqap_approvedtype', 'special external releasing');
            })->where('spexgc_released', 'released')
            ->orderByDesc('spexgc_id')
            ->paginate()->withQueryString();

        return inertia('Treasury/Dashboard/SpecialGc/SpecialReleasedGc', [
            'title' => $promo == '0' ? 'Released Special External Gc' : 'Released Special Internal Gc',
            'filters' => $request->only(['date', 'search']),
            'data' => SpecialExternalGcRequestResource::collection($record),
            'columns' => ColumnHelper::$specialReleasedGc,
            'tab' => $promo
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

    public function approvedRequest(Request $request)
    {
        $promo = $request->has('promo') ? $request->promo : '*';
        $record = SpecialExternalGcrequest::with([
            'approvedRequest' => function ($q) {
                $q->select('reqap_approvedby', 'reqap_trid', 'reqap_date')->where('reqap_approvedtype', 'Special External GC Approved');
            },
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname'
        ])
            ->select('spexgc_id', 'spexgc_company', 'spexgc_num', 'spexgc_datereq', 'spexgc_dateneed')
            ->where('spexgc_promo', $promo)
            ->where('spexgc_status', 'approved')
            ->orderByDesc('spexgc_id')
            ->paginate()->withQueryString();

        return inertia('Treasury/Dashboard/SpecialGc/ApprovedGc', [
            'filters' => $request->only(['date', 'search']),
            'title' => $promo == '0' ? 'Approved Special External Gc' : 'Approved Special Internal Gc',
            'data' => SpecialExternalGcRequestResource::collection($record),
            'columns' => ColumnHelper::$approvedRequest,
            'tab' => $promo
        ]);
    }

    public function viewApprovedRequest(Request $request, SpecialExternalGcrequest $id)
    {
        $data = $id->load([
            'approvedRequest' => function ($q) {
                $q->with('user:user_id,firstname,lastname')
                    ->where('reqap_approvedtype', 'Special External GC Approved')
                    ->select('reqap_trid', 'reqap_preparedby', 'reqap_remarks', 'reqap_doc', 'reqap_checkedby', 'reqap_approvedby', 'reqap_preparedby', 'reqap_date');
            },
            'specialExternalCustomer:spcus_id,spcus_companyname',
            'user:user_id,firstname,lastname',
            'document' => function ($q) {
                $q->where('doc_type', 'Special External GC Request');
            }
        ]);

        $barcodes = SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_trid',
            'spexgcemp_denom',
            'spexgcemp_fname',
            'spexgcemp_lname',
            'spexgcemp_mname',
            'spexgcemp_extname',
            'voucher',
            'address',
            'department',
            'spexgcemp_barcode'
        )->where('spexgcemp_trid', $id->spexgc_id)->paginate()->withQueryString();

        return inertia('Treasury/Dashboard/SpecialGc/Components/ApprovedGcViewing', [
            'title' => 'Special External Gc Requests',
            'records' => new SpecialExternalGcRequestResource($data),
            'barcodes' => $barcodes
        ]);
    }

    public function cancelledRequest(Request $request)
    {
        $record = StoreGcrequest::joinCancelledGcStore()
            ->select('sgc_id', 'sgc_num', 'sgc_date_request', 'sgc_store', 'sgc_requested_by')
            ->where([['sgc_status', 0], ['sgc_cancel', '*']])->paginate();

        return inertia('Treasury/Dashboard/SpecialGc/CancelledSpecialGcRequest', [
            'title' => 'Cancelled Special Gc Request',
            'filters' => $request->only(['date', 'search']),
            'data' => StoreGcRequestResource::collection($record),
            'columns' => ColumnHelper::$cancelledGcRequest
        ]);
    }

    public function viewCancelledRequest(Request $request, $id)
    {
        $cancelled = StoreGcrequest::joinCancelledGcStore()->where('store_gcrequest.sgc_id', $id)->first();
        $denomination = StoreRequestItem::join('denomination', 'denomination.denom_id', '=', 'store_request_items.sri_items_denomination')
            ->select(DB::raw('store_request_items.sri_items_quantity * denomination.denomination as total'), 'store_request_items.sri_items_quantity', 'denomination.denomination')
            ->where('store_request_items.sri_items_requestid', $id);

        $total = NumberHelper::currency($denomination->get()->sum('total'));

        return response()->json([
            'info' => new StoreGcRequestResource($cancelled),
            'denomination' => $denomination->paginate(5),
            'total' => $total
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
