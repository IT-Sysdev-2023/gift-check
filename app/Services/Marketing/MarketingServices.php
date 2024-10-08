<?php

namespace App\Services\Marketing;
use App\Models\ApprovedProductionRequest;
use App\Models\Assignatory;
use App\Models\CancelledProductionRequest;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\PromoGcRequest;
use App\Models\SpecialExternalGcrequest;
use App\Models\Supplier;
use App\Models\UserDetails;
use App\Services\Documents\FileHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

            $selectedData = $selectedData->transform(function ($item) {
                $item->DateRequested = Date::parse($item->pe_date_request)->format('Y-F-d') ?? null;
                $item->DateNeeded = Date::parse($item->pe_date_needed)->format('Y-F-d');
                $item->DateApproved = Date::parse($item->ape_approved_at)->format('Y-F-d');
                $item->aprrovedPreparedBy = ucwords($item->fapproved . ' ' . $item->lapproved);
                $item->RequestPreparedby = ucwords($item->frequest . ' ' . $item->lrequest);

                return $item;
            })->first();
        }


        return $selectedData ?? [];
    }

    public function selectedPromoPendingRequest($request)
    {
        $data = PromoGcRequest::where('pgcreq_id', $request->id)
            ->where('pgcreq_status', 'pending')
            ->where('pgcreq_group_status', '')
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
        $data = [
            'reqNum' => $request->data['requestNo'],
            'dateReq' => Date::parse($request->data['dateRequested'])->format('F d Y'),
            'approvedBy' => strtoupper($request->data['approvedBy']),
            'checkedBy' => strtoupper($request->data['checkedBy'])
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
        $e = SpecialExternalGcrequest::select([
            'special_external_gcrequest.spexgc_num',
            'special_external_gcrequest.spexgc_dateneed',
            'special_external_gcrequest.spexgc_id',
            'special_external_gcrequest.spexgc_datereq',
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as prep"),
            'special_external_customer.spcus_acctname',
            'special_external_customer.spcus_companyname'
        ])
            ->join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('special_external_gcrequest.spexgc_status', 'pending')
            ->where('spexgc_addemp', 'pending')
            ->where('special_external_gcrequest.spexgc_promo', '0')
            ->orderBy('special_external_gcrequest.spexgc_id', 'ASC')
            ->get();

        $i = SpecialExternalGcrequest::select([
            'special_external_gcrequest.spexgc_num',
            'special_external_gcrequest.spexgc_dateneed',
            'special_external_gcrequest.spexgc_id',
            'special_external_gcrequest.spexgc_datereq',
            DB::raw("CONCAT(users.firstname, ' ', users.lastname) as prep"),
            'special_external_customer.spcus_acctname',
            'special_external_customer.spcus_companyname'
        ])
            ->join('users', 'users.user_id', '=', 'special_external_gcrequest.spexgc_reqby')
            ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
            ->where('special_external_gcrequest.spexgc_status', 'pending')
            ->where('spexgc_addemp', 'pending')
            ->where('special_external_gcrequest.spexgc_promo', '*')
            ->orderBy('special_external_gcrequest.spexgc_id', 'ASC')
            ->get();
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
        $cancelled=SpecialExternalGcrequest::where('spexgc_status','cancelled')->count();

        $spgc = [
            'pendingcount' => $pending['internal']->count() + $pending['external']->count(),
            'pending' => $pending,
            'approved' => $approve,
            'cancelled' => $cancelled

        ];
        return $spgc;

    }



}

