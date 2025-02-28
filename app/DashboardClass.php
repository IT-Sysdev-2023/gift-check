<?php

namespace App;

// use App\Http\Requests\DtiGcRequest;
use App\Models\ApprovedGcrequest;
use App\Models\BudgetRequest;
use App\Models\CustodianSrr;
use App\Models\Denomination;
use App\Models\DtiApprovedRequest;
use App\Models\Gc;
use App\Models\InstitutEod;
use App\Models\InstitutTransaction;
use App\Models\LedgerBudget;
use App\Models\LedgerSpgc;
use App\Models\ProductionRequest;
use App\Models\PromoGcReleaseToDetail;
use App\Models\PromoGcRequest;
use App\Models\PromoLedger;
use App\Models\RequisitionForm;
use App\Models\SpecialExternalGcrequest;
use App\Services\Treasury\Dashboard\DashboardService;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;
use App\Models\DtiGcRequest;


class DashboardClass extends DashboardService
{
    /**
     * Create a new class instance.
     */

    public function __construct()
    {
    }
    public function treasuryDashboard(Request $request)
    {
        return [
            'budget' => (object) [
                'totalBudget' => $this->budget(),
                'regularBudget' => LedgerBudget::regularBudget(),
                'specialBudget' => LedgerBudget::specialBudget(),
            ],
            'budgetRequest' => $this->budgetRequestTreasury(),
            'storeGcRequest' => $this->storeGcRequest(),
            'promoGcReleased' => PromoGcReleaseToDetail::count(),
            'institutionGcSales' => InstitutTransaction::count(),
            'gcProductionRequest' => $this->gcProductionRequest(),
            'adjustment' => $this->adjustments(),
            'specialGcRequest' => $this->specialGcRequest(), //Duplicated above use Spatie Permission instead

            'eod' => InstitutEod::count(),
            'productionRequest' => ProductionRequest::where([['pe_generate_code', 0], ['pe_status', 1]])->get()
        ];
    }

    public function retailDashboard()
    {
        //
        return [
            'approved' => ApprovedGcrequest::with('storeGcRequest', 'storeGcRequest.store', 'user')
                ->whereHas('storeGcRequest', function ($query) {
                    $query->where('sgc_store', request()->user()->store_assigned);
                })
                ->count(),
        ];
    }
    public function retailGroupDashboard()
    {
        return [
            'pending' => PromoGcRequest::with('userReqby:user_id,firstname,lastname')
                ->where('pgcreq_group', request()->user()->usergroup)
                ->where('pgcreq_status', 'pending')
                ->where(function ($q) {
                    $q->where('pgcreq_group_status', '')
                        ->orWhere('pgcreq_group_status', 'approved');
                })->count(),
            'approved' => PromoGcRequest::where('pgcreq_status', 'approved')
                ->withWhereHas('approvedReq', function ($q) {
                    $q->where('reqap_approvedtype', 'promo gc preapproved');
                })->count(),
            'promo' => PromoLedger::selectRaw(
                'IFNULL(SUM(promo_ledger.promled_debit - promo_ledger.promled_credit), 0.00) as sum'
            )
                ->join('promo_gc_request', 'promo_gc_request.pgcreq_id', '=', 'promo_ledger.promled_trid')
                ->where('promo_gc_request.pgcreq_group', request()->user()->usergroup)
                ->value('sum'),
        ];
    }
    public function financeDashboard()
    {
        // dd();
        $pendingExternal = SpecialExternalGcrequest::where('spexgc_status', 'pending')
            ->where('spexgc_promo', '0')
            ->where('spexgc_addemp', 'done')
            ->count();
        // dd($pendingExternal);

        $pendingInternal = SpecialExternalGcrequest::where('spexgc_status', 'pending')
            ->where('spexgc_promo', '*')
            ->where('spexgc_addemp', 'done')
            ->count();
        // dd($pendingInternal);

        $curBudget = LedgerBudget::where('bcus_guide', '!=', 'dti')->get();

        $dtiBudget = LedgerBudget::where('bcus_guide', 'dti')->get();

        $ledgerSpgc = LedgerSpgc::get();



        $debitTotal = $curBudget->sum('bdebit_amt');

        $creditTotal = $curBudget->sum('bcredit_amt');

        $dtiDebitTotal = $dtiBudget->sum('bdebit_amt');
        $dtiCreditTotal = $dtiBudget->sum('bcredit_amt');

        $spgcDebitTotal = $ledgerSpgc->sum('spgcledger_debit');

        $spgcreditTotal = $ledgerSpgc->sum('spgcledger_credit');

        return [
            'specialGcRequest' => [
                'pending' => $pendingExternal + $pendingInternal,
                'internal' => $pendingInternal,
                'external' => $pendingExternal,
                'approve' => SpecialExternalGcrequest::where('spexgc_status', 'approved')->count(),
                'cancel' => SpecialExternalGcrequest::where('spexgc_status', 'cancelled')->count(),
            ],
            'budgetRequest' => [
                'pending' => BudgetRequest::where('br_request_status', '0')->where('br_checked_by', '!=', '')
                    ->count(),
                'approved' => BudgetRequest::where('br_request_status', '1')
                    ->count(),
            ],
            'budgetCounts' => [
                'curBudget' => $debitTotal - $creditTotal,
                'dti' => $dtiDebitTotal - $dtiCreditTotal,
                'spgc' => $spgcDebitTotal - $spgcreditTotal,
            ],

            'dtiCounts' => [
                'pending' => DtiGcRequest::where('dti_status', 'pending')->count(),

                'approved' => DtiGcRequest::where('dti_gc_requests.dti_status', 'approved')
                    ->join('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
                    ->leftJoin('dti_barcodes', 'dti_barcodes.dti_trid', '=', 'dti_gc_requests.dti_num')
                    ->count(),

                'cancelled' => DtiGcRequest::with('specialDtiGcrequestItemsHasMany')
                    ->join('users', 'users.user_id', '=', 'dti_gc_requests.dti_cancelled_by')
                    ->where('dti_gc_requests.dti_status', 'cancelled')
                    ->count(),
            ],
            'appPromoCount' => PromoGcRequest::with('userReqby')
                ->whereFilterForApproved()
                ->selectPromoRequest()
                ->count(),
            'penPomoCount' => PromoGcRequest::with('userReqby')
                ->whereFilterForPending()
                ->selectPromoRequest()
                ->count(),
        ];
    }
    public function budgetRequest()
    {
        $data = BudgetRequest::where('br_checked_by', null)
            ->where('br_requested_by', '!=', '')
            ->where('br_request_status', '0')
            ->first();
        if ($data) {
            $data->datereq = Date::parse($data->br_requested_at)->toFormattedDateString();
        }
        return $data;
    }
    public function marketingDashboard()
    {
        //
    }
    public function iadDashboard()
    {
        return [
            'countReceiving' => RequisitionForm::where('used', null)->count(),

            'reviewedCount' => SpecialExternalGcrequest::join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
                ->leftJoin('approved_request', 'reqap_trid', '=', 'spexgc_id')
                ->where('spexgc_reviewed', 'reviewed')
                ->where('reqap_approvedtype', 'Special External GC Approved')->count(),

            'approvedgc' => SpecialExternalGcrequest::where([['spexgc_status', 'approved'], ['spexgc_reviewed', '']])->count(),

            'receivedcount' => CustodianSrr::count()
        ];
    }
    public function custodianDashboard()
    {
        return [
            'countIntExRequest' => SpecialExternalGcrequest::selectFilterEntry()
                ->with('user:user_id,firstname,lastname', 'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname', 'specialExternalGcrequestItemsHasMany:specit_trid,specit_denoms,specit_qty')
                ->where('spexgc_status', 'pending')
                ->where('spexgc_addemp', 'pending')
                ->orderByDesc('spexgc_num')
                ->count(),

            'countApproved' => SpecialExternalGcrequest::with('specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname')
                ->selectFilterApproved()
                ->leftJoin('approved_request', 'reqap_trid', '=', 'spexgc_id')
                ->where('spexgc_status', 'approved')
                ->where('reqap_approvedtype', 'Special External GC Approved')->count(),
            'countReleased' => SpecialExternalGcrequest::join('approved_request', 'reqap_trid', 'spexgc_id')
                ->where('spexgc_released', 'released')
                ->where('reqap_approvedtype', 'special external releasing')
                ->count(),

        ];
    }
    public function custodianDashboardGetDenom()
    {
        $data = Denomination::select(
            'denom_id',
            'denomination',
        )->where('denom_type', 'RSGC')->where('denom_status', 'active')->orderBy('denomination')->get();

        $data->transform(function ($item) {
            $item->count = Gc::where('denom_id', $item->denom_id)
                ->where('gc_ispromo', '')->where('gc_validated', '*')->where('gc_treasury_release', '')->where('gc_allocated', '')->count();
            return $item;
        });

        return $data;
    }
    public function accountingDashboard()
    {
        return [
            'pending' => SpecialExternalGcrequest::with('user')
                ->join('special_external_customer', 'spcus_id', '=', 'spexgc_company')
                ->where('spexgc_status', 'pending')
                ->where('spexgc_promo', '0')
                ->count()
        ];
    }
}
