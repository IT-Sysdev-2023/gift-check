<?php

namespace App\Services\Marketing;
use App\Models\ApprovedProductionRequest;
use App\Models\ApprovedRequest;
use App\Models\Assignatory;
use App\Models\CancelledProductionRequest;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\PromoGcRequest;
use App\Models\PromoGcRequestItem;
use App\Models\SpecialExternalCustomer;
use App\Models\SpecialExternalGcrequest;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserDetails;
use App\Services\Documents\FileHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class MarketingServices extends FileHandler
{
    public function __construct()
    {
        parent::__construct();
        $this->folderName = 'promoGcUpload';
    }
    public function promoGcRequest()
    {
        $promoGcRequest = [
            'pendingRequest' => PromoGcRequest::join('users', 'users.user_id', '=', 'promo_gc_request.pgcreq_reqby')
                ->where('promo_gc_request.pgcreq_group', '!=', '')
                ->where('promo_gc_request.pgcreq_tagged', '1')
                ->where(function ($query) {
                    $query->where(function ($query) {
                        $query->where('promo_gc_request.pgcreq_group_status', '')
                            ->where('promo_gc_request.pgcreq_status', 'pending');
                    })
                        ->orWhere(function ($query) {
                            $query->where('promo_gc_request.pgcreq_group_status', 'approved')
                                ->where('promo_gc_request.pgcreq_status', 'pending');
                        });
                })
                ->count(),

            'approvedRequest' => PromoGcRequest::where('pgcreq_status', 'approved')->count(),
            'cancelledRequest' => PromoGcRequest::where('pgcreq_group_status', 'cancelled')->count(),
        ];
        return $promoGcRequest;
    }

    public function productionRequest()
    {
        $productionRequest = [
            'pendingRequest' => ProductionRequest::where('pe_status', '0')->count(),
            'approvedRequest' => ProductionRequest::where('pe_status', '1')->count(),
            'cancelledRequest' => ProductionRequest::where('pe_status', '2')->count()
        ];
        return $productionRequest;
    }
    public function currentBudget()
    {
        $budgetRow = LedgerBudget::where('bcus_guide', '!=', 'dti')
            ->selectRaw('SUM(bdebit_amt) as total_debit, SUM(bcredit_amt) as total_credit')
            ->first();
        $debit = $budgetRow->total_debit;
        $credit = $budgetRow->total_credit;
        $budget = $debit - $credit;
        $currentBudget = number_format($budget, 2);

        return $currentBudget;
    }

    public function checkedBy()
    {
        $checkedBy = Assignatory::where('assig_dept', auth()->user()->usertype)
            ->orWhere('assig_dept', 1)
            ->get();
        return $checkedBy;
    }

    public function promoGcrequestPendingList()
    {
        $data = PromoGcRequest::join('users', 'users.user_id', '=', 'promo_gc_request.pgcreq_reqby')
            ->where('promo_gc_request.pgcreq_group', '!=', '')
            ->where('promo_gc_request.pgcreq_tagged', '1')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('promo_gc_request.pgcreq_group_status', '')
                        ->where('promo_gc_request.pgcreq_status', 'pending');
                })
                    ->orWhere(function ($query) {
                        $query->where('promo_gc_request.pgcreq_group_status', 'approved')
                            ->where('promo_gc_request.pgcreq_status', 'pending');
                    });
            })
            ->paginate(10)
            ->withQueryString();

        $data->transform(function ($item) {
            $item->dateRequested = Date::parse($item->pgcreq_datereq)->format('F-d-Y');
            $item->dateNeeded = Date::parse($item->pgcreq_dateneeded)->format('F-d-Y');
            $item->RequestedBy = ucwords($item->firstname . ' ' . $item->lastname);
            return $item;
        });
        return $data;
    }

    public function approvedProductionRequest($request)
    {
        $query = ProductionRequest::join('approved_production_request', 'production_request.pe_id', '=', 'approved_production_request.ape_pro_request_id')
            ->join('users', 'users.user_id', '=', 'production_request.pe_requested_by')
            ->leftJoin('users as approvedBy', 'approvedBy.user_id', '=', 'approved_production_request.ape_approved_by') // Fix alias here
            ->select(
                'production_request.*',
                'users.firstname as requestByFirstname',
                'users.lastname as requestByLastname',
                'approvedBy.firstname as approvedByFirstname',
                'approvedBy.lastname as approvedByLastname'
            )
            ->where('pe_status', '1')
            ->where('pe_requisition', '1')
            ->whereAny([
                'pe_num',
                'pe_date_request',
                'users.firstname',
                'users.lastname',
            ], 'LIKE', '%' . $request->search . '%')
            ->orderByDesc('pe_id')
            ->paginate(10)
            ->withQueryString();

        $query->transform(function ($item) {
            $item->Reqprepared = ucwords($item->requestByFirstname . ' ' . $item->requestByLastname);
            $item->ApprovedBy = ucwords($item->approvedByFirstname . ' ' . $item->approvedByLastname);
            $item->dateReq = Date::parse($item->pe_date_request)->format('Y-m-d');
            $item->dateNeed = Date::parse($item->pe_date_needed)->format('Y-m-d');
            $item->ape_approved_at = Date::parse($item->ape_approved_at)->format('Y-m-d');
            return $item;
        });

        return empty($query) ? [] : $query;
    }
    public function approveProductionRequestSelectedData($request, $query)
    {
        if ($query->count() !== 0) {
            $selectedData = collect([
                ProductionRequest::select(
                    'production_request.pe_id',
                    'production_request.pe_num',
                    'production_request.pe_requested_by',
                    'production_request.pe_date_request',
                    'production_request.pe_date_needed',
                    'production_request.pe_file_docno',
                    'production_request.pe_remarks',
                    'production_request.pe_generate_code',
                    'production_request.pe_requisition',
                    'approved_production_request.ape_approved_by',
                    'approved_production_request.ape_remarks',
                    'approved_production_request.ape_approved_at',
                    'approved_production_request.ape_preparedby',
                    'approved_production_request.ape_checked_by',
                    'requestby.firstname as frequest',
                    'requestby.lastname as lrequest',
                    'approvedby.firstname as fapproved',
                    'approvedby.lastname as lapproved',
                    'production_request.pe_type',
                    'production_request.pe_group'
                )
                    ->join('approved_production_request', 'production_request.pe_id', '=', 'approved_production_request.ape_pro_request_id')
                    ->join('users as requestby', 'requestby.user_id', '=', 'production_request.pe_requested_by')
                    ->join('users as approvedby', 'approvedby.user_id', '=', 'approved_production_request.ape_preparedby')
                    ->when($request->id ?? null, function ($query) use ($request) {
                        $query->where('production_request.pe_id', $request->id);
                    })
                    ->when($request->id === null, function ($item) use ($query) {
                        $item->where('production_request.pe_id', $query->first()->pe_id);
                    })
                    ->first()
            ]);

            $approvedBy = User::where('user_id', $selectedData[0]->ape_approved_by)->first();
            $checkby = User::where('user_id', $selectedData[0]['ape_checked_by'])->first();


            $selectedData->transform(function ($item) use ($approvedBy, $checkby) {
                $item->DateRequested = Date::parse($item->pe_date_request)->format('Y-F-d') ?? null;
                $item->DateNeeded = Date::parse($item->pe_date_needed)->format('Y-F-d');
                $item->DateApproved = Date::parse($item->ape_approved_at)->format('Y-F-d');
                $item->aprrovedPreparedBy = ucwords($item->fapproved . ' ' . $item->lapproved);
                $item->RequestPreparedby = ucwords($item->frequest . ' ' . $item->lrequest);
                $item->approvedBy = $approvedBy ? ucwords($approvedBy['firstname'] . ' ' . $approvedBy['lastname']) : '';
                $item->checkby = $checkby ? ucwords($checkby['firstname'] . ' ' . $checkby['lastname']) : '';

                return $item;
            })->first();
        }
        // dd($selectedData->toArray());
        return $selectedData ?? [];
    }

    public function selectedPromoPendingRequest($request)
    {
        $data = PromoGcRequest::where('pgcreq_id', $request->id)
            ->join('users', 'users.user_id', '=', 'promo_gc_request.pgcreq_reqby')
            ->get();
        $data->transform(function ($item) {
            $item->dateRequested = Date::parse($item->pgcreq_datereq)->format('F-d-Y');
            $item->dateNeeded = Date::parse($item->pgcreq_dateneeded)->format('F-d-Y');
            $item->RequestedBy = ucwords($item->firstname . ' ' . $item->lastname);
            return $item;
        });

        return $data;
    }



    public function generatepdfrequisition($request)
    {

        $supplier = Supplier::where('gcs_id', $request->data['selectedSupplierId'])->first();
        function reorderName($name)
        {
            $nameParts = explode(', ', strtoupper($name));
            return trim($nameParts[1] . ' ' . $nameParts[0]);
        }
        $approvedBy = reorderName($request->data['approvedBy']);
        $checkedBy = reorderName($request->data['checkedBy']);

        $data = [
            'reqNum' => $request->data['requestNo'],
            'dateReq' => Date::parse($request->data['dateRequested'])->format('F d Y'),
            'approvedBy' => $approvedBy,
            'checkedBy' => $checkedBy
        ];

        $pdf = Pdf::loadView('pdf/eRequisitionform', [
            'data' => $data,
            'barcodes' => $request->denom,
            'supplier' => $supplier
        ])->setPaper('A4');

        $fileName = $request->data['requestNo'] . '.pdf';


        $storeSuccess = Storage::disk('public')->put('e-requisitionform/' . $fileName, $pdf->output());

        if ($storeSuccess) {
            return $pdf;
        } else {
            return false;
        }
    }

    public function fileUpload(Request $request, $reqNum)
    {
        // return $request->all();
        if ($request->hasFile('file')) {
            $file = $reqNum . '.jpg';
            $this->saveFile($request, $file);

            return $file;
        }
    }

    public function handleManagerApproval($request, $prid)
    {


        $lnum = LedgerBudget::select(['bledger_no'])->orderByDesc('bledger_id')->first();
        $ledgerNumber = intval(optional($lnum)->bledger_no) + 1;

        $query = ProductionRequest::select(
            'production_request.pe_id',
            'users.firstname',
            'users.lastname',
            'production_request.pe_file_docno',
            'production_request.pe_date_needed',
            'production_request.pe_remarks',
            'production_request.pe_num',
            'production_request.pe_date_request',
            'production_request.pe_type',
            'production_request.pe_group',
            'access_page.title'
        )
            ->join('users', 'users.user_id', '=', 'production_request.pe_requested_by')
            ->join('access_page', 'access_page.access_no', '=', 'users.usertype')
            ->where('production_request.pe_id', $prid)
            ->where('production_request.pe_status', 0)
            ->get();

        $insertLedgerBudget = LedgerBudget::create([
            'bledger_no' => $ledgerNumber,
            'bledger_trid' => $prid,
            'bledger_datetime' => now(),
            'bledger_type' => 'RFGCP',
            'bcredit_amt' => $request->data['total'],
            'bledger_typeid' => $query[0]->pe_type,
            'bledger_group' => $query[0]->pe_group
        ]);

        if ($insertLedgerBudget) {
            $insertApprovedRequest = ApprovedProductionRequest::create([
                'ape_pro_request_id' => $prid,
                'ape_approved_by' => $request->user()->user_id,
                'ape_checked_by' => null,
                'ape_remarks' => $request->data['remarks'],
                'ape_approved_at' => now(),
                'ape_file_doc_no' => '',
                'ape_preparedby' => $request->data['requestedById'],
                'ape_ledgernum' => $ledgerNumber
            ]);

            if ($insertApprovedRequest) {
                return true;
            }
        }
    }

    public function handleUserRole0Approval($request, $prid)
    {
        $checked = ApprovedProductionRequest::where('ape_pro_request_id', $prid)
            ->update(['ape_checked_by' => $request->user()->user_id]);
        if ($checked) {
            $productionRequestStatus = ProductionRequest::where('pe_id', $prid)->value('pe_status');
            if ($productionRequestStatus == 0) {
                $isApproved = ProductionRequest::where('pe_id', $prid)
                    ->where('pe_status', '0')
                    ->update(['pe_status' => $request->data['status']]);

                if ($isApproved) {
                    return true;
                }
            }
        }

    }

    public function generateProductionRequestPDF(Request $request)
    {
        $barcodes = $request->barcode;

        $reviewerPosition = UserDetails::where('user_id', $request->data['reviewedById'])->first();
        $approverPosition = UserDetails::where('user_id', $request->data['approvedById'])->first();

        $data = [
            'pr_no' => $request->data['pe_no'],
            'dateRequested' => $request->data['dateRequested'],
            'currentBudget' => $this->currentBudget(),
            'Remarks' => $request->data['InputRemarks'],
            'reviewedBy' => strtoupper($request->data['reviewedBy']),
            'reviewerPosition' => $reviewerPosition['details']['employee_position'],
            'approvedBy' => strtoupper($request->data['approvedBy']),
            'approvedByPosition' => $approverPosition->details['employee_position'],
            'preparedBy' => strtoupper($request->data['preparedBy']),
        ];
        $pdf = Pdf::loadView('pdf/productionrequest', [
            'data' => $data,
            'barcodes' => $barcodes
        ])->setPaper('A4');

        $fileName = $data['pr_no'] . '.pdf';
        Storage::disk('public')->put('approvalform/' . $fileName, $pdf->output());

        return $pdf;
    }

    public function handleRequestCancellation($request, $prid)
    {
        $productionRequestStatus = ProductionRequest::where('pe_id', $prid)->value('pe_status');
        if ($productionRequestStatus == 0) {
            $insertCancel = CancelledProductionRequest::create([
                'cpr_pro_id' => $prid,
                'cpr_at' => now()->format('Y-m-d h:i:s'),
                'cpr_by' => $request->user()->user_id,
                'cpr_isrequis_cancel' => '1',
                'cpr_ldgerid' => null,
                'remarks' => $request->data['cancelremarks']
            ]);
            if ($insertCancel) {
                $cancelled = ProductionRequest::where('pe_id', $prid)
                    ->where('pe_status', '0')
                    ->update(['pe_status' => $request->data['status']]);

                if ($cancelled) {
                    return true;
                }
            }
        }
    }

    public function countspecialgc()
    {
        $e = SpecialExternalGcrequest::join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_gcrequest_items', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_items.specit_trid')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('special_external_gcrequest.spexgc_status', 'pending')
            ->where('spexgc_addemp', 'pending')
            ->where('special_external_gcrequest.spexgc_promo', '0')
            ->orderBy('special_external_gcrequest.spexgc_id', 'ASC')
            ->get();

        $i = SpecialExternalGcrequest::join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_gcrequest_items', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_items.specit_trid')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('special_external_gcrequest.spexgc_status', 'pending')
            ->where('spexgc_addemp', 'pending')
            ->where('special_external_gcrequest.spexgc_promo', '*')
            ->orderBy('special_external_gcrequest.spexgc_id', 'ASC')
            ->get();

        $transformItem = function ($item) {
            $item->dateNeed = Date::parse($item->spexgc_dateneed)->format('F d Y');
            $item->dateReq = Date::parse($item->spexgc_datereq)->format('F d Y');
            $item->totalDenom = number_format($item->specit_denoms * $item->specit_qty, 2);
            $item->requestedBy = ucwords($item->firstname . ' ' . $item->lastname);
            return $item;
        };

        $i->transform($transformItem);
        $e->transform($transformItem);


        $pending = [
            'internal' => $i,
            'external' => $e,
        ];

        $approve = SpecialExternalGcrequest::select(
            'special_external_gcrequest.spexgc_id',
            'special_external_gcrequest.spexgc_num',
            'special_external_gcrequest.spexgc_datereq',
            'special_external_gcrequest.spexgc_dateneed',
            'approved_request.reqap_approvedby',
            'approved_request.reqap_date',
            'special_external_customer.spcus_acctname',
            'special_external_customer.spcus_companyname'
        )
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->leftJoin('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->where('special_external_gcrequest.spexgc_status', 'approved')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->get();


        $cancelled = SpecialExternalGcrequest::where('spexgc_status', 'cancelled')->count();

        $spgc = [
            'pendingcount' => $pending['internal']->count() + $pending['external']->count(),
            'pending' => $pending,
            'approved' => $approve,
            'cancelled' => $cancelled

        ];
        return $spgc;

    }

    public function viewspecialgc($type, $id)
    {
        $transformItem = function ($item) {
            $item->dateNeed = Date::parse($item->spexgc_dateneed)->format('F d Y');
            $item->dateReq = Date::parse($item->spexgc_datereq)->format('F d Y');
            $item->totalDenom = number_format($item->specit_denoms * $item->specit_qty, 2);
            $item->requestedBy = ucwords($item->firstname . ' ' . $item->lastname);
            $item->numbertowords = Number::spell($item->spexgc_balance) . ' peso(s)';
            $item->totalnumbertowords = ucwords(Number::spell($item->specit_denoms * $item->specit_qty) . ' peso(s)');
            return $item;
        };

        if ($type === 'internal') {
            $i = SpecialExternalGcrequest::join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
                ->join('special_external_gcrequest_items', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_items.specit_trid')
                ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
                ->where('special_external_gcrequest.spexgc_id', $id)
                ->where('special_external_gcrequest.spexgc_status', 'pending')
                ->where('spexgc_addemp', 'pending')
                ->where('special_external_gcrequest.spexgc_promo', '*')
                ->orderBy('special_external_gcrequest.spexgc_id', 'ASC')
                ->get();
            $i->transform($transformItem);
        } else {
            $e = SpecialExternalGcrequest::join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
                ->join('special_external_gcrequest_items', 'special_external_gcrequest.spexgc_id', '=', 'special_external_gcrequest_items.specit_trid')
                ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
                ->where('special_external_gcrequest.spexgc_id', $id)
                ->where('special_external_gcrequest.spexgc_status', 'pending')
                ->where('spexgc_addemp', 'pending')
                ->where('special_external_gcrequest.spexgc_promo', '0')
                ->orderBy('special_external_gcrequest.spexgc_id', 'ASC')
                ->get();
            $e->transform($transformItem);
        }

        // Return the result
        return isset($i) ? $i : $e;
    }


    public function approvedSpecialExternalRequest($search)
    {
        $query = SpecialExternalGcrequest::select([
            'special_external_gcrequest.spexgc_id',
            'special_external_gcrequest.spexgc_num',
            'special_external_gcrequest.spexgc_datereq',
            'special_external_gcrequest.spexgc_dateneed',
            'approved_request.reqap_approvedby',
            'approved_request.reqap_date',
            'special_external_customer.spcus_acctname',
            'special_external_customer.spcus_companyname',
        ])
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->leftJoin('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->orWhereAny([
                'spexgc_id',
                'spexgc_num',
                'spcus_companyname',
                'reqap_approvedby',
            ], 'like', '%' . $search . '%')
            ->where('special_external_gcrequest.spexgc_status', 'approved')
            ->where('approved_request.reqap_approvedtype', 'Special External GC Approved')
            ->orderByDesc('special_external_gcrequest.spexgc_id')
            ->paginate(10)
            ->withQueryString();

        $query->transform(function ($item) {
            $item->dateReq = $item->spexgc_datereq->toFormattedDateString();
            $item->dateNeed = $item->spexgc_dateneed->toFormattedDateString();
            $item->dateApproved = Date::parse($item->reqap_date)->format('M d, Y');
            $item->appBy = ucwords($item->reqap_approvedby);
            return $item;
        });
        return $query;
    }


    public function promoApprovedlist()
    {
        $query = PromoGcRequest::select([
            'promo_gc_request.pgcreq_id',
            'promo_gc_request.pgcreq_reqnum',
            'promo_gc_request.pgcreq_datereq',
            'promo_gc_request.pgcreq_dateneeded',
            'promo_gc_request.pgcreq_total',
            'promo_gc_request.pgcreq_group',
            DB::raw("CONCAT(prepby.firstname, ' ', prepby.lastname) as prepby"),
            DB::raw("CONCAT(recom.firstname, ' ', recom.lastname) as recby")
        ])
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'promo_gc_request.pgcreq_id')
            ->join('users as prepby', 'prepby.user_id', '=', 'promo_gc_request.pgcreq_reqby')
            ->join('users as recom', 'recom.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('promo_gc_request.pgcreq_status', 'approved')
            ->where('approved_request.reqap_approvedtype', 'promo gc preapproved')
            ->orderByDesc('pgcreq_id')
            ->get();

        $query = PromoGcRequest::select([
            'promo_gc_request.pgcreq_id',
            'promo_gc_request.pgcreq_reqnum',
            'promo_gc_request.pgcreq_datereq',
            'promo_gc_request.pgcreq_dateneeded',
            'promo_gc_request.pgcreq_total',
            'promo_gc_request.pgcreq_group',
            DB::raw("CONCAT(prepby.firstname, ' ', prepby.lastname) as prepby"),
            DB::raw("CONCAT(recom.firstname, ' ', recom.lastname) as recby")
        ])
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'promo_gc_request.pgcreq_id')
            ->join('users as prepby', 'prepby.user_id', '=', 'promo_gc_request.pgcreq_reqby')
            ->join('users as recom', 'recom.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('promo_gc_request.pgcreq_status', 'approved')
            ->where('approved_request.reqap_approvedtype', 'promo gc preapproved')
            ->orderByDesc('pgcreq_id')
            ->paginate(10)
            ->withQueryString();

        $query->transform(function ($item) {
            $item->dateRequested = Date::parse($item->pgcreq_datereq)->format('F d, Y');
            $item->dateNeeded = Date::parse($item->pgcreq_dateneeded)->format('F d, Y');
            $item->recommendedBy = ucwords($item->recby);
            $item->requestedBy = ucwords($item->prepby);
            $approvedBy = ApprovedRequest::select([
                DB::raw("CONCAT(users.firstname, ' ', users.lastname) as appby")
            ])
                ->join('users', 'users.user_id', '=', 'approved_request.reqap_preparedby')
                ->where('approved_request.reqap_trid', $item->pgcreq_id)
                ->where('approved_request.reqap_approvedtype', 'promo gc approved')
                ->first();
            $item->approvedBy = $approvedBy ? ucwords($approvedBy->appby) : null;

            return $item;
        });

        return $query;
    }


    public function selectedpromogcrequest($request)
    {
        $promoGcRequest = PromoGcRequest::select(
            'promo_gc_request.pgcreq_id',
            'promo_gc_request.pgcreq_reqnum',
            'promo_gc_request.pgcreq_datereq',
            'promo_gc_request.pgcreq_dateneeded',
            'promo_gc_request.pgcreq_doc',
            'promo_gc_request.pgcreq_status',
            'promo_gc_request.pgcreq_group',
            'promo_gc_request.pgcreq_remarks',
            'promo_gc_request.pgcreq_total',
            DB::raw("CONCAT(prep.firstname, ' ', prep.lastname) as prepby"),
            'approved_request.reqap_remarks',
            'approved_request.reqap_doc',
            'approved_request.reqap_date',
            DB::raw("CONCAT(recom.firstname, ' ', recom.lastname) as recomby")
        )
            ->join('users as prep', 'prep.user_id', '=', 'promo_gc_request.pgcreq_reqby')
            ->join('approved_request', 'approved_request.reqap_trid', '=', 'promo_gc_request.pgcreq_id')
            ->join('users as recom', 'recom.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('promo_gc_request.pgcreq_id', $request->id)
            ->where('promo_gc_request.pgcreq_status', 'approved')
            ->where('approved_request.reqap_approvedtype', 'promo gc preapproved')
            ->get();

        $promoGcRequest->transform(function ($item) {
            $item->dateRequest = Date::parse($item->pgcreq_datereq)->format('F d Y');
            $item->dateNeeded = Date::parse($item->pgcreq_dateneeded)->format('F d Y');
            $item->requestedBy = ucwords($item->prepby);
            ;
            $item->requestApprovedDate = Date::parse($item->reqap_date)->format('F d Y');
            $item->requestApprovedTime = Date::parse($item->reqap_date)->format('h:i A');
            $item->recommendedBy = ucwords($item->recomby);

            return $item;
        });


        return $promoGcRequest;
    }


    public function approvedRequests($request)
    {
        $approvedRequests = ApprovedRequest::select(
            'approved_request.reqap_remarks',
            'approved_request.reqap_doc',
            'approved_request.reqap_approvedby',
            'approved_request.reqap_checkedby',
            'approved_request.reqap_date',
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as appby"),
            DB::raw("CONCAT(approvedBy.firstname, ' ', approvedBy.lastname) as approvedBy"),
            DB::raw("CONCAT(checkBy.firstname, ' ', checkBy.lastname) as checkBy")
        )
            ->join('users', 'users.user_id', '=', 'approved_request.reqap_preparedby')
            ->leftJoin('users as approvedBy', 'approvedBy.user_id', '=', 'approved_request.reqap_approvedby')
            ->leftJoin('users as checkBy', 'checkBy.user_id', '=', 'approved_request.reqap_checkedby')
            ->where('approved_request.reqap_approvedtype', 'promo gc approved')
            ->where('approved_request.reqap_trid', $request->id)
            ->get()
            ->transform(function ($item) {
                $item->approvedBy = ucwords($item->approvedBy);
                $item->checkedBy = ucwords($item->checkBy);
                $item->requestApprovedDate = Date::parse($item->reqap_date)->format('F d, Y');
                $item->requestApprovedTime = Date::parse($item->reqap_date)->format('h:i A');
                $item->prepby = ucwords($item->appby);
                return $item;
            });

        return $approvedRequests;
    }

    public function getbarcode($request)
    {
        $barcode = PromoGcRequestItem::where('pgcreqi_trid', $request->id)
            ->join('denomination', 'denomination.denom_id', '=', 'promo_gc_request_items.pgcreqi_denom')
            ->get();

        return $barcode;
    }

    public function viewReleasedSpexGc($request)
    {
        $data = SpecialExternalGcrequest::select([
            'special_external_gcrequest.spexgc_id',
            'special_external_gcrequest.spexgc_num',
            DB::raw("CONCAT(req.firstname, ' ', req.lastname) as reqby"),
            'special_external_gcrequest.spexgc_datereq',
            'special_external_gcrequest.spexgc_dateneed',
            'special_external_gcrequest.spexgc_remarks',
            'special_external_gcrequest.spexgc_payment',
            'special_external_gcrequest.spexgc_paymentype',
            'special_external_gcrequest.spexgc_receviedby',
            'special_external_customer.spcus_acctname',
            'special_external_customer.spcus_companyname',
            'approved_request.reqap_remarks',
            'approved_request.reqap_doc',
            'approved_request.reqap_checkedby',
            'approved_request.reqap_approvedby',
            'approved_request.reqap_preparedby',
            'approved_request.reqap_date',
            DB::raw("CONCAT(prep.firstname, ' ', prep.lastname) as prepby")
        ])
            ->join('users as req', 'req.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')

            ->join('approved_request', 'approved_request.reqap_trid', '=', 'special_external_gcrequest.spexgc_id')
            ->join('users as prep', 'prep.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('special_external_gcrequest.spexgc_status', '=', 'approved')
            ->where('special_external_gcrequest.spexgc_id', '=', $request->id)
            ->where('approved_request.reqap_approvedtype', '=', 'Special External GC Approved')
            ->get();
        $data->transform(function ($item) {
            $item->preparedBy = ucwords($item->prepby);
            $item->requestedBy = ucwords($item->reqby);
            $item->datereq = Date::parse($item->spexgc_datereq)->format('F d, Y');
            $item->validity = Date::parse($item->spexgc_dateneed)->format('F d, Y');
            return $item;
        });

        $revdetails = DB::table('approved_request')
            ->select([
                'approved_request.reqap_remarks',
                'approved_request.reqap_date',
                DB::raw("CONCAT(users.firstname, ' ', users.lastname) as rev")
            ])
            ->join('users', 'users.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('approved_request.reqap_trid', '=', $request->id)
            ->where('approved_request.reqap_approvedtype', '=', 'special external gc review')
            ->get();

        $releaseDetails = ApprovedRequest::select(
            'approved_request.reqap_remarks',
            'approved_request.reqap_date',
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as relby")
        )
            ->join('users', 'users.user_id', '=', 'approved_request.reqap_preparedby')
            ->where('approved_request.reqap_trid', $request->id)
            ->where('approved_request.reqap_approvedtype', 'special external releasing')
            ->get();
        // $releaseDetails->transform();


        return [
            'data' => $data,
            'revdetails' => $revdetails,
            'releaseDetails' => $releaseDetails
        ];
    }




}

