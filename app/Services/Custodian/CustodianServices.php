<?php

namespace App\Services\Custodian;

use App\Helpers\ColumnHelper;
use App\Helpers\NumberHelper;
use App\Helpers\NumberInWordsHelper;
use App\Http\Resources\CustodianSrrResource;
use App\Http\Resources\SpecialGcRequestResource;
use App\Models\BarcodeChecker;
use App\Models\CancelledProductionRequest;
use App\Models\CustodianSrr;
use App\Models\CustodianSrrItem;
use App\Models\Denomination;
use App\Models\Document;
use App\Models\DtiBarcodes;
use App\Models\Gc;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Models\RequisitionEntry;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestEmpAssign;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use App\Services\Custodian\CustodianDbServices;
use App\Services\Documents\FileHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Milon\Barcode\DNS1D;
use Illuminate\Support\Str;

class CustodianServices extends FileHandler
{
    protected $formatter;

    public function __construct(public CustodianDbServices $custodianDbServices)
    {
        parent::__construct();
        $this->folderName = 'custodian';

        $this->formatter = [
            '0',
            'GC E-REQUISITION NO',
            'Receiving No',
            'Transaction Date',
            'Reference No',
            'Purchase Order No',
            'Purchase Date',
            'Reference PO No',
            'Payment Terms',
            'Location Code',
            'Department Code',
            'Supplier Name',
            'Mode of Payment',
            'Remarks',
            'Prepared By',
            'Checked By',
            'SRR Type',
        ];
    }
    public function barcodeChecker()
    {
        $data = BarcodeChecker::with(
            'users:user_id,firstname,lastname',
            'gc:barcode_no,denom_id',
            'gc.denomination:denom_id,denomination'
        )
            ->orderByDesc('bcheck_date')
            ->limit(10)->get();

        $data->transform(function ($item) {
            $item->fullname = $item->users->full_name;
            $item->bcheck_date = Date::parse($item->bcheck_date)->toFormattedDateString();
            $item->denomination = NumberHelper::currency($item->gc->denomination->denomination) ?? null;
            return $item;
        });

        return $data;
    }
    public function searchBarcode($request)
    {
        $data = BarcodeChecker::with(
            'users:user_id,firstname,lastname',
            'gc:barcode_no,denom_id',
            'gc.denomination:denom_id,denomination'
        )->where('bcheck_barcode', $request->search)
            ->orderByDesc('bcheck_date')
            ->limit(1)->get();

        $data->transform(function ($item) {
            $item->bcheck_date = Date::parse($item->bcheck_date)->toFormattedDateString();
            return $item;
        });
        return $data;
    }
    public function reqularGcScannedCount()
    {
        return BarcodeChecker::whereHas('gc', function ($query) {
            $query->whereColumn('barcode_no', 'bcheck_barcode');
        })->count();
    }

    public function specialExternalGcCount()
    {
        return BarcodeChecker::whereHas('special', function ($query) {
            $query->whereColumn('spexgcemp_barcode', 'bcheck_barcode');
        })->count();
    }

    public function totalGcCount()
    {
        return BarcodeChecker::with('gc')->count();
    }

    public function todaysCount()
    {
        return BarcodeChecker::whereDate('bcheck_date', today())->count();
    }

    public function scannedBarcodeFn($request)
    {
        $isInGc = Gc::where('barcode_no', $request->barcode)->exists();

        $isInBc = BarcodeChecker::where('bcheck_barcode', $request->barcode)->exists();

        $scanby = BarcodeChecker::with('scannedBy:user_id,firstname,lastname')
            ->where('bcheck_barcode', $request->barcode)
            ->first();

        if ($isInGc && !$isInBc) {

            BarcodeChecker::create([
                'bcheck_barcode' => $request->barcode,
                'bcheck_checkby' => $request->user()->user_id,
                'bcheck_date' => now(),
            ]);

            return redirect()->back()->with([
                'title' => 'Scan Successfully',
                'status' => 'success',
                'msg' => 'The Barcode ' . $request->barcode . ' Scanned Successfully',
            ]);
        } elseif ($isInBc) {
            return redirect()->back()->with([
                'title' => 'Already Scanned',
                'status' => 'warning',
                'msg' => 'The Barcode ' . $request->barcode . ' is Already Scanned By ' . $scanby->scannedBy->full_name,
            ]);
        } else {
            return redirect()->back()->with([
                'title' => '404 not Found!',
                'status' => 'error',
                'msg' => 'Oppss! The Barcode ' . $request->barcode . ' not found',
            ]);
        }
    }

    public function receivedgcIndex($request)
    {
        $search = $request->search;
        $collection = CustodianSrr::with('user:user_id,firstname,lastname')
            ->select('csrr_id', 'csrr_datetime', 'requis_erno', 'gcs_companyname', 'csrr_receivetype', 'csrr_prepared_by')
            ->join('requisition_entry', 'requis_id', '=', 'csrr_requisition')
            ->join('supplier', 'gcs_id', '=', 'requis_supplierid')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('csrr_id', 'like', '%' . $search . '%')
                        ->orWhere('requis_erno', 'like', '%' . $search . '%')
                        ->orWhere('gcs_companyname', 'like', '%' . $search . '%')
                        ->orWhere('csrr_datetime', 'like', '%' . $search . '%')
                        ->orWhere('csrr_receivetype', 'like', '%' . $search . '%')
                        ->orWhere('csrr_prepared_by', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
                        });
                });
            })
            ->orderByDesc('csrr_id')
            ->paginate(10)
            ->withQueryString();

        return CustodianSrrResource::collection($collection);
    }

    public function specialExternalGcEntry($request)
    {
        $key = $request->activeKey == '2' ? '*' : '0';

        $data = SpecialExternalGcrequest::selectFilterEntry()
            ->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'specialExternalGcrequestItemsHasMany:specit_trid,specit_denoms,specit_qty')
            ->where('spexgc_status', 'pending')
            ->where('spexgc_addemp', 'pending')
            ->where('spexgc_promo', $key)
            ->orderByDesc('spexgc_num')
            ->get();

        return SpecialGcRequestResource::collection($data);
    }

    public function specialExternalGcSetup($request)
    {
        $data = SpecialExternalGcrequest::selectFilterSetup()
            ->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'specialExternalGcrequestItemsHasMany:specit_trid,specit_denoms,specit_qty')
            ->where('spexgc_id', $request->id)
            ->where('spexgc_status', 'pending')
            ->get();

        $count = 1;

        $data->transform(function ($item) use (&$count) {

            $item->specialExternalGcrequestItemsHasMany->each(function ($subitem) use (&$count) {
                $subitem->tempId = $count++;
                $subitem->subtotal = $subitem->specit_denoms * $subitem->specit_qty;
                return $subitem;
            });
            $item->dateneeded = Date::parse($item->spexgc_dateneed)->toFormattedDateString();
            $item->datereq = Date::parse($item->spexgc_datereq)->toFormattedDateString();

            $item->numberinwords = Number::spell($item->spexgc_payment) . ' pesos';

            $item->total = $item->specialExternalGcrequestItemsHasMany->sum('subtotal');

            return $item;
        });


        return $data;
    }

    public function submitSpecialExternalGc($request)
    {
        DB::transaction(function () use ($request) {
            $this->custodianDbServices
                ->specialGcExternalEmpAssign($request)
                ->updateSpecialExtRequest($request->reqid);
        });

        return redirect()->route('custodian.dashboard')->with([
            'status' => 'success',
            'msg' => 'Successfully Submitted Form',
            'title' => 'Success',
        ]);
    }

    public function approvedGcList($request)
    {
        $search = $request->search;
        $internalSearch1 = $request->internalSearch1;
        $data = SpecialExternalGcrequest::with('specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
            ->selectFilterApproved()
            ->leftJoin('approved_request', 'reqap_trid', '=', 'spexgc_id')
            ->where('spexgc_promo', $request->promo ?? '0')
            ->where('spexgc_status', 'approved')
            ->where('reqap_approvedtype', 'Special External GC Approved')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('spexgc_num', 'like', '%' . $search . '%')
                        ->orWhere('reqap_approvedby', 'like', '%' . $search . '%')
                        ->orWhereHas('specialExternalCustomer', function ($query) use ($search) {
                            $query->where('spcus_companyname', 'like', '%' . $search . '%')
                                ->orWhere('spcus_acctname', 'like', '%' . $search . '%');
                        })
                        ->orWhere('spexgc_datereq', 'like', '%' . $search . '%')
                        ->orWhere('spexgc_dateneed', 'like', '%' . $search . '%');
                });
            })
            ->when($internalSearch1, function ($query) use ($internalSearch1) {
                $query->where(function ($query) use ($internalSearch1) {
                    $query->where('spexgc_num', 'like', '%' . $internalSearch1 . '%')
                        ->orWhere('reqap_approvedby', 'like', '%' . $internalSearch1 . '%')
                        ->orWhereHas('specialExternalCustomer', function ($query) use ($internalSearch1) {
                            $query->where('spcus_companyname', 'like', '%' . $internalSearch1 . '%')
                                ->orWhere('spcus_acctname', 'like', '%' . $internalSearch1 . '%');
                        })
                        ->orWhere('spexgc_datereq', 'like', '%' . $internalSearch1 . '%')
                        ->orWhere('spexgc_dateneed', 'like', '%' . $internalSearch1 . '%');
                });
            })
            ->orderByDesc('spexgc_num')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {

            $item->company = $item->specialExternalCustomer->spcus_companyname;

            $item->datereq = Date::parse($item->spexgc_datereq)->toFormattedDateString();
            $item->dateneeded = Date::parse($item->spexgc_dateneed)->toFormattedDateString();
            $item->reqap_date = Date::parse($item->reqap_date)->toFormattedDateString();

            return $item;
        });

        return [
            'data' => $data,
            'search' => $search,
            'internalSearch' => $internalSearch1
        ];
    }

    public function setupApprovalSelected($request)
    {

        $docs = Document::where('doc_trid', $request->id)
            ->where('doc_type', 'Special External GC Request')
            ->first();

        $special = SpecialExternalGcrequest::with('user:user_id,firstname,lastname', 'specialExternalCustomer', 'approvedRequest.user')
            ->selectFilterSetupApproval()
            ->withWhereHas('approvedRequest', function ($query) {
                $query->where('reqap_approvedtype', 'Special External GC Approved');
            })
            ->where('spexgc_status', 'approved')
            ->where('spexgc_id', $request->id)
            ->first();

        if ($special) {
            $special->image = $special->approvedRequest->reqap_doc;
            $special->dateneeded = Date::parse($special->spexgc_dateneed)->toFormattedDateString();
            $special->datereq = Date::parse($special->spexgc_datereq)->toFormattedDateString();
            $special->dateapp = Date::parse($special->approvedRequest->reqap_date)->toFormattedDateString();
        }

        if ($special) {
            if ($special->spexgc_paymentype == '1') {
                $special->paymentStatus = 'Cash';
            } elseif ($special->spexgc_paymentype == '2') {
                $special->paymentStatus = 'Check';
            } elseif ($special->spexgc_paymentype == '3') {
                $special->paymentStatus = 'JV';
            } elseif ($special->spexgc_paymentype == '4') {
                $special->paymentStatus = 'AR';
            } elseif ($special->spexgc_paymentype == '5') {
                $special->paymentStatus = 'On Account';
            }
        }


        return (object) [
            'docs' => $docs,
            'special' => $special,
        ];
    }

    public function setupApprovalBarcodes($request)
    {

        $data = SpecialExternalGcrequestEmpAssign::select(
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
        )->where('spexgcemp_trid', $request->id)->get();

        $data->transform(function ($item) {
            $item->completename = $item->spexgcemp_fname . ' ' . $item->spexgcemp_mname . ' ' . $item->spexgcemp_lname . ' ' . $item->spexgcemp_extname;
            return $item;
        });

        return $data;
    }

    public function getSpecialExternalGcRequest($request)
    {
        $data = SpecialExternalGcrequestEmpAssign::with(
            'specialExternalGcRequest:spexgc_id,spexgc_company',
            'specialExternalGcRequest.specialExternalCustomer:spcus_id,spcus_acctname'
        );

        $data = match ($request->status) {
            '1' => $data->where('spexgcemp_barcode', $request->barcode)->get(),
            '2' => $data->whereBetween('spexgcemp_barcode', [$request->barcodeStart, $request->barcodeEnd])->get(),
        };


        $data->transform(function ($item) {
            // dd($item->specialExternalGcRequest->specialExternalCustomer->spcus_acctname);
            $holdername = Str::ucfirst($item->spexgcemp_fname) . ', ' .
                Str::ucfirst($item->spexgcemp_lname) . ' ' .
                Str::ucfirst($item->spexgcemp_mname) . '' .
                Str::ucfirst($item->spexgcemp_extname);

            $barcode = new DNS1D();

            $html = $barcode->getBarcodePNG($item->spexgcemp_barcode, 'C128');

            $item->barcode = $html;
            $item->numWords = Number::spell($item->spexgcemp_denom) . ' pesos only';
            $item->holder = $holdername;
            $item->custname = $item->specialExternalGcRequest->specialExternalCustomer->spcus_acctname;
            return $item;
        });

        return $data;
    }

    public function getSpecialExternalGcRequestDti($request)
    {
        $data = DtiBarcodes::with(
            'dtigcrequest:dti_num,dti_company',
            'dtigcrequest.specialExternalCustomer:spcus_id,spcus_acctname'
        );

        $data = match ($request->status) {
            '2' => $data->where('dti_barcode', $request->barcode)->get(),
            '1' => $data->whereBetween('dti_barcode', [$request->barcodestart, $request->barcodeend])->get(),
        };

        $data->transform(function ($item) {
            $holdername = Str::ucfirst($item->fname) . ', ' .
                Str::ucfirst($item->lname) . ' ' .
                Str::ucfirst($item->mname) . '' .
                Str::ucfirst($item->extname);

            $barcode = new DNS1D();

            $html = $barcode->getBarcodePNG($item->dti_barcode, 'C128');

            $item->barcode = $html;
            $item->numWords = Number::spell($item->dti_denom) . ' pesos only';
            $item->holder = $holdername;
            $item->custname = $item->dtigcrequest->specialExternalCustomer->spcus_acctname;
            return $item;
        });

        return $data;
    }

    public function upload($request)
    {
        if ($request->file[0]['originFileObj']->isValid()) {

            $path = $request->file[0]['originFileObj']->getRealPath();
            $contents = file_get_contents($path);
        }

        $exp = $this->explode($contents);

        $collection2 = collect($exp);

        $missingKeys = collect($this->formatter)->filter(function ($value) use ($collection2) {
            return !$collection2->has($value);
        });

        if ($missingKeys->isEmpty()) {
            $this->insertIntoDatabase($exp);
        } else {
            return redirect()->back()->with([
                'msg' => 'Not a valid format',
                'data' => $missingKeys,
                'status' => 'error',
                'title' => 'Invalid Format'
            ]);
        }
    }

    public function insertIntoDatabase($exp)
    {
        dd($exp);
    }

    private function explode($data)
    {
        $lines = explode("\n", $data);

        $parsedData = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (!empty($line)) {

                if (strpos($line, '|') !== false) {
                    list($key, $value) = explode('|', $line);
                    $parsedData[trim($key)] = trim($value);
                } else {
                    $parsedData[] = $line;
                }
            }
        }

        return $parsedData;
    }

    public function getProductionApproved($request)
    {
        $search = $request->search;
        $data = ProductionRequest::select(
            'pe_id',
            'pe_num',
            'pe_date_request',
            'ape_approved_at',
            'pe_date_needed',
            'firstname',
            'ape_approved_by',
            'lastname',
        )->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('pe_num', 'like', '%' . $search . '%')
                    ->orWhere('pe_date_request', 'like', '%' . $search . '%')
                    ->orWhere('ape_approved_at', 'like', '%' . $search . '%')
                    ->orWhere('pe_date_needed', 'like', '%' . $search . '%')
                    ->orWhere('ape_approved_by', 'like', '%' . $search . '%')
                    ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ['%' . $search . '%']);
            });
        })
            ->join('approved_production_request', 'ape_pro_request_id', '=', 'pe_id')
            ->join('users', 'user_id', 'pe_requested_by')
            ->where('pe_status', '1')
            ->orderByDesc('pe_id')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {
            $item->pe_date_request_tran = Date::parse($item->pe_date_request)->toFormattedDateString();
            $item->ape_approved_at_tran = Date::parse($item->ape_approved_at)->toFormattedDateString();
            $item->reqby = Str::ucfirst($item->firstname) . ' ' . Str::ucfirst($item->lastname);

            return $item;
        });

        return $data;
    }

    public function getApprovedDetails($id)
    {
        return (object) [
            'items' => $this->getPeDenomination($id),
            'column' => ColumnHelper::$approved_details_column,
            'approved' => $this->approved($id),
        ];
    }

    private function getPeDenomination($id)
    {
        $data = ProductionRequestItem::join('denomination', 'denom_id', '=', 'pe_items_denomination')
            ->where('pe_items_request_id', $id)
            ->get();

        $data->transform(function ($item) use ($id) {
            $item->bstart = Gc::where('denom_id', $item->pe_items_denomination)->where('pe_entry_gc', $id)->orderByDesc('barcode_no')->value('barcode_no');
            $item->bend = Gc::where('denom_id', $item->pe_items_denomination)->where('pe_entry_gc', $id)->orderBy('barcode_no')->value('barcode_no');
            $item->fsubt = NumberHelper::currency($item->denomination * $item->pe_items_quantity);
            $item->nfsubt = $item->denomination * $item->pe_items_quantity;
            $item->uom = 'pc(s)';
            return $item;
        });

        $total = $data->sum('nfsubt');

        return (object) [
            'data' => $data,
            'total' => NumberHelper::currency($total),
        ];
    }

    private function approved($id)
    {
        return ProductionRequest::selectFilterApproved()
            ->join('approved_production_request', 'ape_pro_request_id', '=', 'pe_id')
            ->join('users as reqby', 'reqby.user_id', '=', 'pe_requested_by')
            ->join('users as appby', 'appby.user_id', '=', 'ape_preparedby')
            ->where('pe_id', $id)
            ->first();
    }

    public function getBarcodeApprovedDetails($request, $id)
    {
        $data = Gc::select('denomination.denomination', 'gc.denom_id')
            ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->where('pe_entry_gc', $id)
            ->where('gc_validated', $request->status === null ? '' : $request->status)
            ->get()
            ->groupBy('denomination');

        $data->transform(function ($item, $key) {
            return [
                'denom_id' => $item[0]->denom_id
            ];
        });

        return response()->json([
            'record' => $data,
        ]);
    }

    public function getEveryBarcodeDetails($request, $id)
    {

        $data = Gc::select('denomination.denomination', 'barcode_no', 'gc.denom_id')
            ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
            ->where('gc_validated', $request->status === null ? '' : $request->status)
            ->where('gc.denom_id', $request->key)
            ->where('pe_entry_gc', $id)
            ->get();

        return response()->json([
            'record' => $data
        ]);
    }
    public function getRequisitionDetailsData($id)
    {
        return RequisitionEntry::select(
            'requis_erno',
            'requis_req',
            'pe_date_needed',
            'requis_loc',
            'requis_dept',
            'requis_rem',
            'requis_checked',
            'requis_approved',
            'gcs_companyname',
            'gcs_contactperson',
            'gcs_contactnumber',
            'gcs_address',
            'firstname',
            'lastname',
        )->join('production_request', 'pe_id', '=', 'repuis_pro_id')
            ->join('supplier', 'gcs_id', '=', 'requis_supplierid')
            ->join('users', 'user_id', '=', 'requis_req_by')
            ->where('repuis_pro_id', $id)
            ->first();
    }

    public function getCancelledViewing($request)
    {
        $search = $request->search;
        $data = CancelledProductionRequest::select(
            'pe_id',
            'pe_num',
            'pe_date_request',
            'cpr_at',
            'req.firstname as rfname',
            'req.lastname as rlname',
            'can.firstname as cfname',
            'can.lastname as clname',
        )->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('pe_num', 'like', '%' . $search . '%')
                    ->orWhere('pe_date_request', 'like', '%' . $search . '%')
                    ->orWhere('cpr_at', 'like', '%' . $search . '%')
                    ->orWhereRaw("CONCAT(req.firstname, ' ',req.lastname ) like ? ", ['%' . $search . '%'])
                    ->orWhereRaw("CONCAT(can.firstname, ' ', can.lastname) LIKE ?", ['%' . $search . '%']);
            });
        })
            ->join('production_request', 'pe_id', '=', 'cpr_pro_id')
            ->join('users as req', 'req.user_id', '=', 'pe_requested_by')
            ->join('users as can', 'can.user_id', '=', 'cpr_by')
            ->orderByDesc('cpr_id')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {

            $item->req_date = Date::parse($item->pe_date_request)->toFormattedDateString();
            $item->can_at = Date::parse($item->cpr_at)->toFormattedDateString();
            $item->prepby = Str::ucfirst($item->rfname) . ' ,' . Str::ucfirst($item->rlname);
            $item->canby = Str::ucfirst($item->cfname) . ' ,' . Str::ucfirst($item->clname);

            return $item;
        });

        return $data;
    }
    public function getProductionCancelledDetails($id)
    {
        $data = ProductionRequest::select(
            'pe_num',
            'pe_requested_by',
            'pe_date_request',
            'remarks',
            'cpr_at',
            'req.firstname as rfname',
            'req.lastname as rlname',
            'can.firstname as cfname',
            'can.lastname as clname',
        )
            ->join('cancelled_production_request', 'cpr_pro_id', '=', 'pe_id')
            ->join('users as req', 'req.user_id', '=', 'pe_requested_by')
            ->join('users as can', 'can.user_id', '=', 'cpr_by')
            ->where('pe_status', '2')
            ->where('pe_id', $id)
            ->first();

        if ($data) {
            $data->req_date = Date::parse($data->pe_date_request)->toFormattedDateString();
            $data->can_at = Date::parse($data->cpr_at)->toFormattedDateString();
            $data->prepby = Str::ucfirst($data->rfname) . ' ,' . Str::ucfirst($data->rlname);
            $data->canby = Str::ucfirst($data->cfname) . ' ,' . Str::ucfirst($data->clname);
        }

        $barcode = ProductionRequestItem::select(
            'pe_items_request_id',
            'pe_items_denomination',
        )
            ->where('pe_items_request_id', $id)
            ->join('denomination', 'pe_items_denomination', '=', 'denom_id')
            ->get();

        $barcode->transform(function ($item) use ($id) {
            $item->start = Gc::where('pe_entry_gc', $id)->where('denom_id', $item->pe_items_denomination)->orderBy('barcode_no')->value('barcode_no');
            $item->end = Gc::where('pe_entry_gc', $id)->where('denom_id', $item->pe_items_denomination)->orderByDesc('barcode_no')->value('barcode_no');
            return $item;
        });

        return (object) [
            'data' => $data,
            'barcode' => $barcode,
        ];
    }
    public function getAvailableGcRecords()
    {
        $data = Gc::select(
            'barcode_no',
            'denom_id',
            'csrr_prepared_by',
            'cssitem_recnum',
            'csrr_datetime',
            'csrr_id'
        )
            ->join('custodian_srr_items', 'cssitem_barcode', '=', 'barcode_no')
            ->join('custodian_srr', 'csrr_id', '=', 'cssitem_recnum')
            ->where('gc_validated', '*')
            ->where('gc_allocated', '')
            ->where('gc_ispromo', '')
            ->where('gc_treasury_release', '')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {
            $item->valBy = User::select('firstname', 'lastname')->where('user_id', $item->csrr_prepared_by)->value('full_name');
            $item->date = Date::parse($item->csrr_datetime)->toFormattedDateString();
            $item->denom = Denomination::select('denomination')->where('denom_id', $item->denom_id)->value('denomination_format');
            return $item;
        });

        return $data;
    }

    public function gcTrackingSubmission($request)
    {
        $request->validate([
            'barcode' => 'required',
        ]);

        $datagc = Gc::select('denom_id', 'pe_entry_gc')
            ->with('denomination:denom_id,denomination', 'gcbarcodegenerate:gbcg_pro_id,gbcg_at')
            ->where('barcode_no', $request->barcode)
            ->first();

        if ($datagc) {
            $datagc->barcode = $request->barcode;
            $datagc->dateGen = $datagc->gcbarcodegenerate->gbcg_at;
            $datagc->denom = $datagc->denomination->denomination_format;
            $datagc->entry = NumberHelper::leadingZero($datagc->pe_entry_gc);
        }
        $datasrr = CustodianSrrItem::with(
            'custodiaSsr',
            'custodiaSsr.purorderdetails:purchorderdet_ref,purchorderdet_purono',
            'custodiaSsr.user:user_id,firstname,lastname'
        )
            ->where('cssitem_barcode', $request->barcode)
            ->first();

        if ($datasrr) {
            $datasrr->datetime = Date::parse($datasrr->custodiaSsr->csrr_datetime)->toFormattedDateString() ?? null;
            $datasrr->num = $datasrr->cssitem_recnum ?? null;
            $datasrr->purono = $datasrr->custodiaSsr->purorderdetails->purchorderdet_purono ?? null;
            $datasrr->name = $datasrr->custodiaSsr->user->full_name ?? null;
            $datasrr->type = $datasrr->custodiaSsr->csrr_receivetype ?? null;
        }

        return (object) [
            'datagc' => $datagc,
            'datasrr' => $datasrr
        ];
    }

    public function fetchReleased($request)
    {
        $search = $request->search;
        // dd($search);
        $data = SpecialExternalGcrequest::select(
            'spexgc_reqby',
            'spexgc_company',
            'spexgc_id',
            'spexgc_num',
            'spexgc_datereq',
            'spexgc_dateneed',
            'spexgc_id',
            'firstname',
            'lastname',
            'reqap_date'
        )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('spexgc_num', 'like', '%' . $search . '%')
                        ->orWhere('spexgc_datereq', 'like', '%' . $search . '%')
                        ->orWhere('firstname', 'like', '%' . $search . '%')
                        ->orWhere('lastname', 'like', '%' . $search . '%')
                        ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ? ", ['%' . $search . '%'])
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(firstname, ' ', lastname) LIKE ? ", ['%' . $search . '%']);
                        })
                        ->orWhereHas('specialExternalCustomer', function ($query) use ($search) {
                            $query->where('spcus_acctname', 'like', '%' . $search . '%')
                                ->orWhere('spcus_companyname', 'like', '%' . $search . '%');
                        });
                });
            })
            ->join('approved_request', 'reqap_trid', 'spexgc_id')
            ->join('users', 'user_id', 'reqap_preparedby')
            ->with(
                'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
                'user:user_id,firstname,lastname'
            )
            ->where('spexgc_released', 'released')
            ->where('reqap_approvedtype', 'special external releasing')
            ->orderByDesc('spexgc_num')
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {

            return (object) [
                'id' => $item->spexgc_id,
                'num' => $item->spexgc_num,
                'datereq' => Date::parse($item->spexgc_datereq)->toFormattedDateString(),
                'dateneed' => Date::parse($item->spexgc_dateneed)->toFormattedDateString(),
                'reqby' => $item->user->full_name,
                'revby' => Str::ucfirst($item->firstname) . ' ' . Str::ucfirst($item->lastname),
                'acctname' => $item->specialExternalCustomer->spcus_acctname,
                'reqappdate' => Date::parse($item->reqap_date)->toFormattedDateString(),
            ];
        });

        return $data;
    }

    public function fetchReleasedDetails($id)
    {
        $spgc = $this->custodianDbServices->getSpecialExternalGcRequest($id);

        $docs = $this->custodianDbServices->getDocs($id);

        $barcodes = $this->custodianDbServices->getGcSpecialBarcodes($id);

        $approvedReq = $this->custodianDbServices->getApprovedRequest($id);

        $releasedReq = $this->custodianDbServices->getReleasedRequest($id);

        return (object) [
            'spgc' => $spgc,
            'docs' => $docs,
            'approved' => $approvedReq,
            'released' => $releasedReq,
            'barcodes' => $barcodes,
        ];
    }


    public function uploadImageFile($request)
    {

        $filename = $this->getOriginalFileName($request, $request->file);


        return $this->saveFile($request, $filename);
    }
}
