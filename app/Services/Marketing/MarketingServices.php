<?php

namespace App\Services\Marketing;
use App\Models\Assignatory;
use App\Models\LedgerBudget;
use App\Models\ProductionRequest;
use App\Models\PromoGcRequest;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

class MarketingServices
{
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
            'cancelledRequest' => PromoGcRequest::where('pgcreq_status', 'cancel')->count(),
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
            ->where('pe_status', '1')
            ->whereAny(['pe_num'], 'LIKE', '%' . $request->search . '%')
            ->orderByDesc('pe_id')
            ->paginate(10)
            ->withQueryString();
        $query->transform(function ($item) {
            $item->Reqprepared = ucwords($item->firstname . ' ' . $item->lastname);
            $item->dateReq = Date::parse($item->pe_date_request)->format('Y-m-d');
            $item->dateNeed = Date::parse($item->pe_date_needed)->format('Y-m-d');
            $item->ape_approved_at = Date::parse($item->ape_approved_at)->format('Y-m-d');
            return $item;
        });

        return $query;
    }
    public function approveProductionRequestSelectedData($request, $query)
    {
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

        return $selectedData;
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
            'reqNum' => $request->data['productionReqNum'],
            'dateReq' => Date::parse($request->data['dateRequested'])->format('F d Y'),
            'dateNeed' => Date::parse($request->data['dateNeeded'])->format('F d Y'),
            'approvedBy' => strtoupper($request->data['approvedBy']),
            'checkedBy' => strtoupper($request->data['checkedBy'])
        ];

        $pdf = Pdf::loadView('pdf/eRequisitionform', [
            'data' => $data,
            'barcodes' => $request->denom,
            'supplier' => $supplier
        ])->setPaper('A4');

        $fileName = $request->data['productionReqNum'] . '.pdf';

      
        $storeSuccess = Storage::disk('public')->put('e-requisitionform/' . $fileName, $pdf->output());

        if ($storeSuccess) {
            return $pdf; 
        } else {
            return false; 
        }
    }



}

