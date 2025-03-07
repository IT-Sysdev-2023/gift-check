<?php

namespace App\Services\Iad;

use App\Exports\IadPurchased\PurchasedExports;
use App\Exports\SpecialReviewedGcMultipleExports;
use App\Exports\VerifiedGcMultipleSheetExport;
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
use App\Models\Store;
use App\Models\StoreEodTextfileTransaction;
use App\Models\StoreVerification;
use App\Models\Supplier;
use App\Models\TempValidation;
use App\Models\TransactionRevalidation;
use App\Models\User;
use App\Services\Documents\FileHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use App\Traits\Iad\AuditTraits;
use App\Traits\OpenOfficeTraits\StorePurchasedTraits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class IadServices extends FileHandler
{
    use AuditTraits;
    use StorePurchasedTraits;

    public function __construct(public IadDbServices $iadDbServices)
    {
        $this->initializeSpreadsheet();
        parent::__construct();
    }
    public function gcReceivingIndex($request)
    {
        // dd();
        $search = $request->search;
        return RequisitionForm::where('used', null)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('rec_no', 'like', '%' . $search . '%')
                        ->orWhere('req_no', 'like', '%' . $search . '%')
                        ->orWhere('trans_date', 'like', '%' . $search . '%')
                        ->orWhere('sup_name', 'like', '%' . $search . '%')
                        ->orWhere('po_no', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();
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

        $requisProId = self::getRequistionNo($request->requisId) ?? null;

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

            $this->iadDbServices->custodianSsrCreate($request)
                ->custodianPurchaseOrderDetails($request)
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
    public function getReviewedGc($request)
    {
        $search = $request->search;
        $data = SpecialExternalGcrequest::select(
            'spexgc_id',
            'spexgc_num',
            'spexgc_datereq',
            'reqap_approvedby',
            'reqap_date',
            'spcus_acctname',
            'spcus_companyname',
            'reqap_trid',
        )->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('spexgc_id', 'like', '%' . $search . '%')
                    ->orWhere('spexgc_num', 'like', '%' . $search . '%')
                    ->orWhere('spexgc_datereq', 'like', '%' . $search . '%')
                    ->orWhere('reqap_approvedby', 'like', '%' . $search . '%')
                    ->orWhere('reqap_date', 'like', '%' . $search . '%')
                    ->orWhere('spcus_acctname', 'like', '%' . $search . '%')
                    ->orWhere('spcus_companyname', 'like', '%' . $search . '%')
                    ->orWhere('reqap_trid', 'like', '%' . $search . '%');
            });
        })
            ->join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
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
        )->leftJoin('special_external_bank_payment_info', 'spexgcbi_id', '=', 'spexgc_id')
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

    public function getReceivedGc($request)
    {
        $searchTerm = $request->search;
        $data = CustodianSrr::select(
            'csrr_id',
            'csrr_receivetype',
            'csrr_datetime',
            'csrr_prepared_by',
            'csrr_requisition'
        )
            ->when($searchTerm, function ($query) use ($searchTerm) {
                $query->where(function ($subQuery) use ($searchTerm) {
                    $subQuery->where('csrr_id', 'like', '%' . $searchTerm . '%')
                        ->orWhere('csrr_receivetype', 'like', '%' . $searchTerm . '%')
                        ->orWhere('csrr_datetime', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('requisition.supplier', function ($query) use ($searchTerm) {
                            $query->where('gcs_companyname', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhereHas('requisition', function ($query) use ($searchTerm) {
                            $query->where('requis_erno', 'like', '%' . $searchTerm . '%')
                                ->orWhere('requis_supplierid', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhereHas('user', function ($query) use ($searchTerm) {
                            $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $searchTerm . '%']);
                        });
                });
            })
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
            return redirect()->back()->with([
                'stream' => $stream,
                'msg' => 'SuccessFully Submitted!',
                'title' => 'Success',
                'status' => 'success',
            ]);
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
                    'position' => 'Department Head'
                ]
            ]
        ];
        $pdf = Pdf::loadView('pdf.giftcheck', ['data' => $data]);

        $this->folderName = 'generatedTreasuryPdf/BudgetRequest';

        $this->savePdfFile($request, $bud->br_no, $pdf->output());

        return base64_encode($pdf->output());
    }

    public function getDetails($id)
    {
        $budget = BudgetRequest::where('br_id', $id)->first();

        if ($budget) {
            $budget->requested = Date::parse($budget->br_requested_at)->toFormattedDateString();
            $budget->reqby = User::select('firstname', 'lastname')->where('user_id', $budget->br_requested_by)->value('full_name');
        }

        return $budget;
    }

    public function getAuditStore($request)
    {

        $date = empty($request->date) ? [] : [$request->date[0], $request->date[1]];

        $traits = $this->dataTraits($request, $date);

        $traits->begbal->transform(function ($item) {
            $item->subtformat = NumberHelper::currency($item->first()->denomination * $item->count());
            $item->subtotal = $item->first()->denomination * $item->count();
            return $item;
        });

        $traits->gcrelease->transform(function ($item) use ($date) {
            if (!empty($date)) {
                $item->barcodest = $this->getBarcodes($item, $date)->orderBy('barcode', 'ASC')->first()->barcode ?? null;
                $item->barcodelt = $this->getBarcodes($item, $date)->orderBy('barcode', 'DESC')->first()->barcode ?? null;
                $item->subtformat = NumberHelper::currency($item->denom * $item->count);
                $item->subtotal = $item->denom * $item->count;
                return $item;
            }
        });

        $addedgc = $this->transform($traits->addedgc);
        $unusedgc = $this->transform($traits->unusedgc);

        $con = (!empty($date) && $date[0] !== null);

        return (object) [
            'addedgc' => $con ? $addedgc->values() : [],
            'gcsold' => $con ? $traits->gcrelease->values() : [],
            'unusedgc' => ($con && $date[0] !== null) ? $unusedgc->values() : [],
            'begbal' => $traits->begbal->sum('subtotal'),
            'gcsoldbal' => $traits->gcrelease->sum('subtotal'),
            'unusedbal' => $traits->unusedgc->sum('subtotal'),
            'datebackend' => $con ?  $date  : [],
            'date' =>  $con ? Date::parse($date[0])->toFormattedDateString() . ' to ' . Date::parse($date[1])->toFormattedDateString() : 'No Date Selected',
        ];
    }

    private static function transform($data)
    {
        return $data->transform(function ($item) {
            return (object) [
                'count' => $item->count(),
                'barcodest' => $item->first()->barcode_no,
                'barcodelt' => $item->last()->barcode_no,
                'denom' => $item->first()->denomination,
                'subtformat' => NumberHelper::currency($item->count() * $item->first()->denomination),
                'subtotal' => $item->count() * $item->first()->denomination,
            ];
        });
    }

    public function generateAudited($data)
    {
        $pdf = Pdf::loadView('pdf.auditstore', ['data' => $data]);

        return response()->json([
            'stream' => base64_encode(string: $pdf->output())
        ]);
    }
    public function getVerifiedSoldUsedData($request)
    {
        $search = $request->search;

        $storeVerification = StoreVerification::select(
            'vs_barcode',
            'reval_trans_id',
            'reval_barcode',
            'vs_tf_used',
            'vs_tf_denomination',
            'vs_cn',
            'vs_gctype',
            'vs_store',
            'vs_reverifydate',
            'gc_treasury_release',
            'transaction_stores.trans_datetime',
        )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('vs_barcode', 'like', '%' . $search . '%')
                        ->orWhere('reval_trans_id', 'like', '%' . $search . '%')
                        ->orWhere('reval_barcode', 'like', '%' . $search . '%')
                        ->orWhere('vs_tf_used', 'like', '%' . $search . '%')
                        ->orWhere('vs_tf_denomination', 'like', '%' . $search . '%')
                        ->orWhere('vs_cn', 'like', '%' . $search . '%')
                        ->orWhere('vs_gctype', 'like', '%' . $search . '%')
                        ->orWhere('vs_store', 'like', '%' . $search . '%')
                        ->orWhere('vs_reverifydate', 'like', '%' . $search . '%')
                        ->orWhere('gc_treasury_release', 'like', '%' . $search . '%')
                        ->orWhere('transaction_stores.trans_datetime', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($query) use ($search) {
                            $query->whereRaw("CONCAT (cus_fname, ' ', cus_lname ) LIKE ?", ['%' . $search . '%']);
                        })
                        ->orWhereHas('store', function ($query) use ($search) {
                            $query->where('store_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('type', function ($query) use ($search) {
                            $query->where('gctype', 'like', '%' . $search . '%');
                        });
                });
            })
            ->with(
                'customer:cus_id,cus_fname,cus_lname,cus_mname,cus_namext',
                'store:store_id,store_name',
                'type:gc_type_id,gctype',
            )->leftJoin('gc', 'barcode_no', '=', 'vs_barcode')
            ->leftJoin('transaction_revalidation', 'reval_barcode', '=', 'vs_barcode')
            ->leftJoin('transaction_stores', 'trans_sid', '=', 'reval_trans_id')
            ->whereRaw('1=1')
            ->orderByDesc('vs_id')
            ->paginate(10)->withQueryString();

        $storeVerification->transform(function ($item) {
            $item->denomination = NumberHelper::currency($item->vs_tf_denomination);
            $item->storename = $item->store->store_name;
            $item->customername = $item->customer->full_name;
            $item->gctype = empty($item->gc_treasury_release) ? Str::ucfirst($item->type->gctype) :  Str::ucfirst($item->type->gctype) . " (Institutional GC)";

            if ($item->vs_gctype === 1 || $item->vs_gctype === 2) {
                if ($item->gc_treasury_release === '*') {
                    $item->soldrel = Date::parse($this->institution($item->vs_barcode))->toFormattedDateString();
                } else {
                    $item->soldrel = Date::parse($this->transactionsales($item->vs_barcode))->toFormattedDateString();
                }
            } elseif ($item->vs_gctype === 3) {
                $item->soldrel = Date::parse($this->special($item->vs_barcode))->toFormattedDateString();
            } elseif ($item->vs_gctype === 4) {
                $item->soldrel = Date::parse($this->promo($item->vs_barcode))->toFormattedDateString();
            }
            return $item;
        });
        return  $storeVerification;
    }

    public function getVerifiedDetails($barcode)
    {
        $store = StoreVerification::select('vs_by', 'vs_date', 'vs_time')
            ->with('user:user_id,firstname,lastname')
            ->where('vs_barcode', $barcode)
            ->first();

        if ($store) {
            $store->time = Date::parse($store->vs_time)->format('H:i a');
            $store->date = Date::parse($store->vs_date)->toFormattedDateString();
        }

        return $store;
    }
    public function getVerifiedsDetails($barcode)
    {
        $data = TransactionRevalidation::with('trans_stores')->join()->where('reval_barcode', $barcode)->first();
    }
    public function getTransactionText($barcode)
    {
        $data = StoreEodTextfileTransaction::where('seodtt_barcode', $barcode)->get();

        return $data;
    }
    public function getVerifiedReports()
    {
        // dd();
    }

    public function getStores()
    {
        $store = Store::get();

        $store->transform(function ($item) {

            return (object) [
                'value' => $item->store_id,
                'label' => $item->store_name,
            ];
        });

        return $store;
    }

    public function generateVerifiedReportExcel($request)
    {
        $rec = new VerifiedGcMultipleSheetExport($request->all());

        return Excel::download($rec, 'users.xlsx');
    }

    public function generatePurchasedReportsExcel($request)
    {
        $rec = new PurchasedExports($request->all());

        return Excel::download($rec, 'users.xlsx');
    }
    public function generatePurchasedReportsOpenOffice($request)
    {
        return $this->record($request)
            ->header()
            ->data()
            ->save();
    }
    public function generateSpecialReviewedReportsExcel($request)
    {
        return Excel::download(new SpecialReviewedGcMultipleExports($request->all()), 'special.xlsx');
    }
    public function reprintRequestFromMarketing($id)
    {

        $folder = "e-requisitionform/";
        $pdfName = '0'.$id;
        return $this->retrieveFile($folder, $pdfName.'.pdf');
    }
}
