<?php

namespace App\Services\Iad;

use App\Helpers\NumberHelper;
use App\Models\ApprovedRequest;
use App\Models\BudgetRequest;
use App\Models\CustodianSrr;
use App\Models\CustodianSrrItem;
use App\Models\Denomination;
use App\Models\Document;
use App\Models\Gc;
use App\Models\LedgerBudget;
use App\Models\ProductionRequestItem;
use App\Models\RequisitionEntry;
use App\Models\RequisitionForm;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\TempValidation;
use App\Models\User;
use App\Services\Documents\FileHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class IadServices extends FileHandler
{

    public function __construct(public IadDbServices $iadDbServices)
    {
        parent::__construct();
    }
    public function gcReceivingIndex()
    {
        return RequisitionForm::where('used', null)
            ->orderByDesc('id')
            ->get();
    }


    public function setupReceivingtxt($request)
    {
        // dd();
        $isEntry = RequisitionEntry::where('requis_erno', $request->requisId)->exists();

        $requisform = RequisitionForm::with('requisFormDenom')->where('req_no', $request->requisId)->first();

        return $isEntry ? $requisform : null;
    }

    public function getRecNum()
    {
        $data =  CustodianSrr::orderByDesc('csrr_id')->first();

        $recnum = !empty($data) ? $data->csrr_id + 1 : 1;

        return $recnum;
    }

    public static function getRequistionNo($requisId)
    {
        return RequisitionEntry::select(
            'requis_erno',
            'requis_id',
            'repuis_pro_id'
        )->where('requis_erno', $requisId)->first()->repuis_pro_id;
    }

    public function getDenomination($denom, $request)
    {
        // dd($denom);


        $requisProId = self::getRequistionNo($request->requisId) ?? null;

        // dd(1);


        $data =  Denomination::select('denomination', 'denom_fad_item_number', 'denom_code', 'denom_id')
            ->where('denom_type', 'RSGC')
            ->where('denom_status', 'active')
            ->get();

        $cssritem = TempValidation::get()->groupBy('tval_denom');

        $countItems = $cssritem->map(function ($item) {
            return $item->count();
        });


        $data->transform(function ($item) use ($denom, $cssritem, $countItems, $requisProId) {

            $prodRequest = ProductionRequestItem::where('pe_items_request_id', $requisProId)
                ->where('pe_items_denomination', $item->denom_id)->first();
            // dd($prodRequest->toArray());

            foreach ($denom as $key => $value) {
                if ($item->denom_fad_item_number == $value->denom_no) {
                    $item->qty = $value->quantity;
                }
            }
            foreach ($countItems as $key => $value) {

                if ($item->denom_id == $key) {
                    $item->scanned =  $value;
                }
            }

            $ifNotNull = !empty($prodRequest->pe_items_denomination) ? $prodRequest->pe_items_denomination : null;

            if ($item->denom_id ===  $ifNotNull) {
                $item->item_remain = $prodRequest->pe_items_remain ?? null;
            }


            return $item;
        });

        // dd($data->toArray());

        return $data;
    }

    public function validateByRangeServices($request)
    {

        $request->validate([
            'barcodeStart' => 'bail|lt:barcodeEnd|min:13|max:13|required',
            'barcodeEnd' => 'bail|gt:barcodeStart|min:13|max:13',
        ]);

        $query = RequisitionEntry::where('requis_erno', $request->reqid)->first();

        $inGc =  $query->where('requis_id',   $query->requis_id)
            ->join('gc', 'pe_entry_gc', '=', 'repuis_pro_id')
            ->whereIn('barcode_no', [$request->barcodeStart, $request->barcodeEnd])
            ->get();

        if ($inGc->count() == 2) {
            $isValidated = CustodianSrrItem::where('cssitem_barcode', [$request->barcodeStart, $request->barcodeEnd])->exists();

            if (!$isValidated) {
                $ifNotScanned = TempValidation::whereIn('tval_barcode', [$request->barcodeEnd, $request->barcodeStart])->count() == 2;

                if (!$ifNotScanned) {
                    $denomid = Gc::select('denom_id')->where('barcode_no', $request->barcodeEnd)->first();

                    foreach (range($request->barcodeStart, $request->barcodeEnd) as $barcode) {
                        TempValidation::create([
                            'tval_barcode' => $barcode,
                            'tval_recnum' => $request->recnum,
                            'tval_denom' => $denomid->denom_id,
                        ]);
                    }

                    return back()->with([
                        'status' => 'success',
                        'title' => 'Success',
                        'msg' => 'Barcode # ' . $request->barcodeStart . ' to ' . $request->barcodeEnd . ' is Validated Successfully',
                    ]);
                } else {
                    return back()->with([
                        'status' => 'warning',
                        'title' => 'Info',
                        'msg' => 'Barcode # ' . $request->barcodeStart . ' to ' . $request->barcodeEnd . ' is Already Scanned!',
                    ]);
                }
            } else {
                return back()->with([
                    'status' => 'warning',
                    'title' => 'Info',
                    'msg' => 'Barcode # ' . $request->barcodeStart . ' is Already Validated!',
                ]);
            }
        } else {
            return back()->with([
                'status' => 'error',
                'title' => 'Error!',
                'msg' => 'Barcode ' . $request->barcodeStart . ' to ' . $request->barcodeEnd . ' not Found! ',
            ]);
        }
    }
    public function getScannedGc()
    {
        return TempValidation::select('denom_id', 'tval_denom', 'tval_barcode', 'denomination')
            ->join('denomination', 'denom_id', '=', 'tval_denom')
            ->get();
    }

    public function validateBarcodeFunction($request)
    {

        $request->validate([
            'barcode' => 'bail|min:13|max:13|required',
        ]);

        $query = RequisitionEntry::where('requis_erno', $request->reqid)->first();

        $inGc =  $query->where('requis_id',   $query->requis_id)
            ->join('gc', 'pe_entry_gc', '=', 'repuis_pro_id')
            ->where('barcode_no', $request->barcode)
            ->exists();



        if ($inGc) {

            $isValidated = CustodianSrrItem::where('cssitem_barcode', $request->barcode)->exists();

            if (!$isValidated) {

                $ifScanned = TempValidation::where('tval_barcode', $request->barcode)->exists();

                if (!$ifScanned) {
                    $denomid = Gc::select('denom_id')->where('barcode_no', $request->barcode)->first();

                    TempValidation::create([
                        'tval_barcode' => $request->barcode,
                        'tval_recnum' => $request->recnum,
                        'tval_denom' => $denomid->denom_id,
                    ]);

                    return back()->with([
                        'status' => 'success',
                        'title' => 'Success',
                        'msg' => 'Barcode # ' . $request->barcode . ' is Validated Successfully',
                    ]);
                } else {
                    return back()->with([
                        'status' => 'warning',
                        'title' => 'Warning!',
                        'msg' => 'Barcode ' . $request->barcode . ' is Already Scanned! ',
                    ]);
                }
            } else {
                return back()->with([
                    'status' => 'warning',
                    'title' => 'Warning!',
                    'msg' => 'Barcode ' . $request->barcode . ' is Already Validated! ',
                ]);
            }
        } else {
            return back()->with([
                'status' => 'error',
                'title' => 'Error!',
                'msg' => 'Barcode ' . $request->barcode . ' not Found! ',
            ]);
        }
    }
    public function submitSetupFunction($request)
    {
        // dd($request->all());
        $request->validate([
            'select' => 'required',
            'scanned' => 'required',
        ]);

        $create =  DB::transaction(function () use ($request) {

            $id = self::getRequistionNo($request->data['req_no']);

            $this->iadDbServices->custodianPurchaseOrderDetails($request)
                ->custodianUsedAndValidated($id, $request->data['req_no'], $request->select)
                ->custodianRequisitionUpdate($request)
                ->custodianUpProdDetails($request)
                ->custodianGcUpdate($request)
                ->custodianSrrItems($request);

            return true;
        });

        if ($create) {
            TempValidation::truncate();

            return redirect()->route('iad.dashboard')->with([
                'status' => 'success',
                'title' => 'Success',
                'msg' => 'Successfully Submitted!',
            ]);
        } else {
            return back()->with([
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'Opss Something Went Wrong!',
            ]);
        }
    }
    public function getReviewedGc()
    {
        $data = SpecialExternalGcrequest::select(
            'spexgc_id',
            'spexgc_num',
            'spexgc_datereq',
            'reqap_approvedby',
            'reqap_date',
            'spcus_acctname',
            'spcus_companyname',
            'reqap_trid',
        )->join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
            ->leftJoin('approved_request', 'reqap_trid', '=', 'spexgc_id')
            ->where('spexgc_reviewed', 'reviewed')
            ->where('reqap_approvedtype', 'Special External GC Approved')
            ->orderByDesc('spexgc_num')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {

            $app = ApprovedRequest::select('reqap_date', 'firstname', 'lastname')
                ->join('users', 'user_id', '=', 'reqap_preparedby')
                ->where('reqap_trid', $item->reqap_trid)
                ->where('reqap_approvedtype', 'special external gc review')
                ->first();

            $fname =  empty($app->firstname) ? null : $app->firstname;
            $lname =  empty($app->lastname) ? null : $app->lastname;
            $dateRev = empty($app->reqap_date) ? null :  Date::parse($app->reqap_date)->toFormattedDateString();

            $item->reqdate = Date::parse($item->spexgc_datereq)->toFormattedDateString();
            $item->appdate = Date::parse($item->reqap_date)->toFormattedDateString();
            $item->fullname =  $fname  . ' , ' . $lname;
            $item->revdate =  $dateRev;

            return $item;
        });

        return $data;
    }

    public function getReviewedDetails($id)
    {
        $data = SpecialExternalGcrequest::select(
            'spexgc_datereq',
            'spexgc_dateneed',
            'spexgc_remarks',
            'spexgc_payment',
            'spexgc_paymentype',
            'spexgc_id',
            'spexgc_company',
            'spexgc_reqby',
        )->with(
            'user:user_id,firstname,lastname',
            'specialExternalCustomer:spcus_id,spcus_companyname',
            'approvedRequest',
            'approvedRequest1.user:user_id,firstname,lastname'
        )->leftJoin('special_external_bank_payment_info', 'spexgcbi_trid', '=', 'spexgc_id')
            ->whereHas('approvedRequest', fn($query) => $query->where('reqap_approvedtype', 'Special External GC Approved'))
            ->where('spexgc_status', 'approved')
            ->where('spexgc_id', $id)
            ->first();

        return $data;
    }

    public function getDocuments($id)
    {

        return  Document::where('doc_trid', $id)
            ->where('doc_type', 'Special External GC Request')
            ->value('doc_fullpath');
    }

    public function specialBarcodes($id)
    {
        return SpecialExternalGcrequestEmpAssign::select(
            'spexgcemp_trid',
            'spexgcemp_denom',
            'spexgcemp_fname',
            'spexgcemp_lname',
            'spexgcemp_mname',
            'spexgcemp_extname',
            'spexgcemp_barcode',
        )->where('spexgcemp_trid', $id)->get();
    }

    public function approvedRequest($id)
    {
        return ApprovedRequest::select('reqap_date', 'reqap_id', 'reqap_preparedby', 'reqap_remarks')
            ->with('user:user_id,lastname,firstname')
            ->where('reqap_trid', $id)
            ->where('reqap_approvedtype', 'special external gc review')
            ->first();
    }

    public function getReceivedGc()
    {
        $data = CustodianSrr::select('csrr_id', 'csrr_receivetype', 'csrr_datetime', 'csrr_prepared_by', 'csrr_requisition')
            ->with(
                'user:user_id,firstname,lastname',
                'requisition:requis_id,requis_supplierid,requis_erno',
                'requisition.supplier:gcs_id,gcs_companyname'
            )
            ->orderByDesc('csrr_id')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {
            $item->recnumber = NumberHelper::leadingZero($item->csrr_id, '%03d');
            $item->requisno = $item->requisition->requis_erno;
            $item->date = Date::parse($item->csrr_datetime)->toFormattedDateString();
            $item->companyname = $item->requisition->supplier->gcs_companyname;
            $item->fullname = $item->user->full_name ?? null;
            return $item;
        });

        return $data;
    }

    public function getReceivedDetails($id)
    {
        $data = CustodianSrrItem::select('cssitem_barcode', 'denomination')
            ->join('gc', 'barcode_no', '=', 'cssitem_barcode')
            ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->where('cssitem_recnum', $id);

        $denom = $data->get()->groupBy('denomination');


        $denomData = $denom->map(function ($group) {
            return $group->count();
        });

        return (object)[
            'total' => $data->count(),
            'record' => $data->get(),
            'denom' =>  $denomData
        ];
    }

    public function updateBudgetRequest($request, $id)
    {
        $update = BudgetRequest::where('br_id', $id)->update([
            'br_checked_by' => request()->user()->user_id,
        ]);

        if ($update) {
            $stream = $this->generatePdf($request, $id);
            return redirect()->back()->with(['stream' => $stream, 'success' => 'SuccessFully Submitted!']);
        }
    }

    private function generatePdf($request, $id)
    {
        $bud = BudgetRequest::where('br_id', $id)->first();

        $appby = User::select('firstname', 'lastname')->where('user_id', $bud->br_requested_by)->first();

        $data = [
            'pr' => $bud->br_no,
            'budget' => NumberHelper::format(LedgerBudget::budget()),
            'dateRequested' => today()->toFormattedDateString(),
            'dateNeeded' => Date::parse($bud->br_requested_needed)->toFormattedDateString(),
            'remarks' => $bud->br_remarks,

            'subtitle' => 'Revolving Budget Entry Form',

            'budgetRequested' => $bud->br_request,
            //signatures

            'signatures' => [
                'preparedBy' => [
                    'name' => $appby->full_name,
                    'position' => 'Sr Cash Clerk'
                ],
                'checkedBy' => [
                    'name' => $request->user()->full_name,
                    'position' => 'Finance Analyst'
                ]
            ]
        ];
        $pdf = Pdf::loadView('pdf.giftcheck', ['data' => $data]);

        $this->folderName = 'generatedTreasuryPdf/BudgetRequest';

        $this->savePdfFile($request, $bud->br_no, $pdf->output());

        return base64_encode($pdf->output());
    }
}
