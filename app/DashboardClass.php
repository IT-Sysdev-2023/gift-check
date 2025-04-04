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
use App\Models\DtiLedgerSpgc;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardClass extends DashboardService
{
    /**
     * Create a new class instance.
     */

    public function __construct() {}
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
            'approvedDti' => DtiGcRequest::where([['dti_status', 'approved'], ['dti_addemp', 'done']])->count(),
            'releasedDti' => DtiApprovedRequest::where('dti_approved_requests.dti_approvedtype', 'special external releasing')->count(),
            'pendingDtiCount' => DtiGcRequest::select('dti_num', 'dti_datereq', 'dti_dateneed', 'firstname', 'lastname', 'spcus_companyname',)
                ->join('users', 'user_id', 'dti_reqby')
                ->join('special_external_customer', 'spcus_id', 'dti_company')
                ->where('dti_status', 'pending')
                ->where('dti_addemp', 'pending')
                ->count(),
            'specialGcRequest' => $this->specialGcRequest(), //Duplicated above use Spatie Permission instead
            // 'dti_req' => $this->
            'eod' => InstitutEod::count(),
            'revcount' => $this->reviewedGcForReleasingCount(),
            'productionRequest' => ProductionRequest::where([['pe_generate_code', 0], ['pe_status', 1]])->get()
        ];
    }

    public function retailDashboard()
    {
        //
        // dd();
        return [
            'approved' => ApprovedGcrequest::with('storeGcRequest', 'storeGcRequest.store', 'user')
                ->whereHas('storeGcRequest', function ($query) {
                    $query->where('sgc_store', request()->user()->store_assigned);
                })
                ->count(),
        ];
    }
    public function reviewedGcForReleasingCount()
    {
        return DtiGcRequest::with([
            'specialExternalCustomer:spcus_id,spcus_acctname,spcus_companyname',
            'user:user_id,firstname,lastname',
            'specialDtiBarcodesHasMany',
            'approvedRequestRevied.user',

        ])
            ->withWhereHas('approvedRequest', function ($q) {
                $q->select('dti_trid', 'dti_approvedby')->where('dti_approvedtype', 'Special External GC Approved');
            })
            ->where([
                ['dti_status', 'approved'],
                ['dti_reviewed', 'reviewed'],
                ['dti_released', null],
                ['dti_promo', 'external']
            ])->count();
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

    public function budget()
    {
        $query = LedgerBudget::select(DB::raw('SUM(bdebit_amt) as debit'), DB::raw('SUM(bcredit_amt) as credit'))
            ->whereNot('bcus_guide', 'dti')
            ->whereNull('bledger_category')
            ->first();

        return bcsub($query->debit, $query->credit, 2);
    }
    public function financeDashboard()
    {
        // dd();
        $pendingExternal = SpecialExternalGcrequest::where('spexgc_status', 'pending')
            ->where('spexgc_promo', '0')
            ->where('spexgc_addemp', operator: 'done')
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

        $dti = DtiLedgerSpgc::get();

        $dtiDebitTotal = $dtiBudget->sum('bdebit_amt');
        $dtiCreditTotal = $dtiBudget->sum('bcredit_amt');

        $spgcDebitTotal = $ledgerSpgc->sum('spgcledger_debit');

        $spgcreditTotal = $ledgerSpgc->sum('spgcledger_credit');

        $dtiDebitNewTotal = $dti->sum('dti_ledger_debit');
        $dtiCreditNewTotal = $dti->sum('dti_ledger_credit');
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
                'curBudget' => $this->budget(),
                'dti' => $dtiDebitTotal - $dtiCreditTotal,
                'spgc' => $spgcDebitTotal - $spgcreditTotal,
                'dti_new' => $dtiDebitNewTotal - $dtiCreditNewTotal,
            ],

            'dtiCounts' => [
                'pending' => DtiGcRequest::where('dti_status', 'pending')
                    ->where('dti_addemp', 'done')
                    ->count(),

                'approved' => DtiGcRequest::with('specialDtiGcrequestItemsHasMany')
                    ->join('users', 'users.user_id', '=', 'dti_gc_requests.dti_reqby')
                    ->join('special_external_customer', 'special_external_customer.spcus_id', '=', 'dti_gc_requests.dti_company')
                    ->join('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
                    ->where('dti_gc_requests.dti_status', 'approved')
                    ->where('dti_approved_requests.dti_approvedtype', 'Special External GC Approved')
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
            $data->reqby = User::select('firstname', 'lastname')->where('user_id', $data->br_requested_by)->value('full_name');
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
            'reviewedCountSpecial' => SpecialExternalGcrequest::select(
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
                ->count(),
            'reviewedCount' => CustodianSrr::select(
                'csrr_id',
                'csrr_receivetype',
                'csrr_datetime',
                'csrr_prepared_by',
                'csrr_requisition'
            )->with(
                'user:user_id,firstname,lastname',
                'requisition:requis_id,requis_supplierid,requis_erno',
                'requisition.supplier:gcs_id,gcs_companyname'
            )
                ->count(),

            'approvedgc' => SpecialExternalGcrequest::where([['spexgc_status', 'approved'], ['spexgc_reviewed', '']])->count(),

            'receivedcount' => CustodianSrr::count(),
            'dtiApprovedCount' => DtiGcRequest::join('special_external_customer', 'special_external_customer.spcus_id', '=', 'dti_gc_requests.id')
                ->join('users', 'users.user_id', '=', 'dti_gc_requests.dti_reqby')
                ->join('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
                ->where('dti_gc_requests.dti_status', 'approved')
                ->where('dti_gc_requests.dti_addemp', 'done')
                ->where('dti_approved_requests.dti_approvedtype', '!=', 'special external gc review')
                ->where('dti_reviewed', null)
                ->count(),

            'dtiReceivedCount' => DtiApprovedRequest::join('dti_gc_requests', 'dti_gc_requests.dti_num', '=', 'dti_approved_requests.dti_trid')
                ->join('users', 'users.user_id', '=', 'dti_approved_requests.dti_preparedby')
                ->where('dti_approved_requests.dti_approvedtype', 'special external gc review')
                ->count(),
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
            'countDtiApproved' => $this->countPendingDti(),

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

    public function countPendingDti()
    {
        return  DtiGcRequest::with('customer:spcus_id,spcus_acctname,spcus_companyname')
            ->leftJoin('dti_approved_requests', 'dti_approved_requests.dti_trid', '=', 'dti_gc_requests.dti_num')
            ->where('dti_status', 'approved')
            ->where('dti_approvedtype', 'Special External GC Approved')->count();
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
