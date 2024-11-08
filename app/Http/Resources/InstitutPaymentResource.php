<?php

namespace App\Http\Resources;

use App\Helpers\NumberHelper;
use App\Models\ApprovedGcrequest;
use App\Models\ApprovedPromorequest;
use App\Models\ApprovedRequest;
use App\Models\GcRelease;
use App\Models\InstitutTransaction;
use App\Models\InstitutTransactionsItem;
use App\Models\PromoGcReleaseToItem;
use App\Models\SpecialExternalGcrequest;
use App\Models\SpecialExternalGcrequestItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitutPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        if ($this->insp_paymentcustomer == 'institution') {
            $r = InstitutTransaction::join('institut_customer', 'institut_customer.ins_id', '=', 'institut_transactions.institutr_cusid')
                ->select(
                    'institut_transactions.institutr_id',
                    'institut_transactions.institutr_trnum',
                    'institut_transactions.institutr_paymenttype',
                    'institut_transactions.institutr_date',
                    'institut_customer.ins_name'
                )
                ->where('institutr_id', $this->insp_trid)
                ->first();

            if ($r) {
                $paymenttype = $r->institutr_paymenttype;

                $customer = $r->ins_name;
                $datetr = $r->institutr_date;

                // $q = InstitutTransactionsItem::selectRaw("IFNULL(COUNT(institut_transactions_items.instituttritems_barcode),0) as cnt,
                //         IFNULL(SUM(denomination.denomination),0) as totamt")
                //     ->join('gc', 'gc.barcode_no', '=', 'institut_transactions_items.instituttritems_barcode')
                //     ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                //     ->where('instituttritems_trid', $this->insp_trid)->first();

                // $totgccnt = $q->cnt;
                // $totdenom = $q->totamt;
            }
        } elseif ($this->insp_paymentcustomer == 'stores') {
            $r = ApprovedGcrequest::join('store_gcrequest', 'store_gcrequest.sgc_id', '=', 'approved_gcrequest.agcr_request_id')
                ->join('stores', 'stores.store_id', '=', 'store_gcrequest.sgc_store')
                ->select(
                    'approved_gcrequest.agcr_request_id',
                    'approved_gcrequest.agcr_request_relnum',
                    'approved_gcrequest.agcr_approved_at',
                    'approved_gcrequest.agcr_paymenttype',
                    'stores.store_name'
                )
                ->where('approved_gcrequest.agcr_id', $this->insp_trid)->first();

            if ($r) {
                $customer = $r->store_name;
                $datetr = $r->agcr_approved_at;

                $paymenttype = $r->agcr_paymenttype;

                // $q = GcRelease::selectRaw("IFNULL(COUNT(gc_release.re_barcode_no),0) as cnt,
                //         IFNULL(SUM(denomination.denomination),0) as totamt")
                //     ->join('gc', 'gc.barcode_no', '=', 'gc_release.re_barcode_no')
                //     ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                //     ->where('rel_num', $r->agcr_request_relnum)->first();

                // $totgccnt = $q->cnt;
                // $totdenom = $q->totamt;
            }
        } elseif ($this->insp_paymentcustomer == 'promo') {
            $r = ApprovedPromorequest::join('promo_gc_request', 'promo_gc_request.pgcreq_id', '=', 'approved_promorequest.apr_request_id')
                ->select(
                    'approved_promorequest.apr_request_id',
                    'approved_promorequest.apr_request_relnum',
                    'approved_promorequest.apr_approved_at',
                    'approved_promorequest.apr_paymenttype',
                    'approved_promorequest.apr_remarks'
                )
                ->where('approved_promorequest.apr_id', $this->insp_trid)->first();

            if ($r) {

                $customer = $r->apr_remarks;
                $datetr = $r->apr_approved_at;

                $paymenttype = $r->apr_paymenttype;

                // $q = PromoGcReleaseToItem::selectRaw("IFNULL(COUNT(promo_gc_release_to_items.prreltoi_barcode),0) as cnt,
                //         IFNULL(SUM(denomination.denomination),0) as totamt")
                //     ->join('gc', 'gc.barcode_no', '=', 'promo_gc_release_to_items.prreltoi_barcode')
                //     ->join('denomination', 'denomination.denom_id', '=', 'gc.denom_id')
                //     ->where('prreltoi_relid', $r->apr_request_id)->first();

                // $totgccnt = $q->cnt;
                // $totdenom = $q->totamt;
            }
        } elseif ($this->insp_paymentcustomer == 'special external') {
            $r = SpecialExternalGcrequest::join('special_external_customer', 'special_external_customer.spcus_id', '=', 'special_external_gcrequest.spexgc_company')
                ->select(
                    'special_external_gcrequest.spexgc_id',
                    'special_external_gcrequest.spexgc_datereq',
                    'special_external_customer.spcus_companyname',
                    'special_external_customer.spcus_acctname',
                    'special_external_gcrequest.spexgc_paymentype'
                )
                ->where('special_external_gcrequest.spexgc_id', $this->insp_trid)->first();

            if ($r) {
                $customer = $r->spcus_acctname;
                $datetr = $r->spexgc_datereq;
                $pymnttype = $r->spexgc_paymentype;

                if ($pymnttype == '1') {
                    $paymenttype = 'cash';
                } elseif ($pymnttype == '2') {
                    $paymenttype = 'check';
                } elseif ($pymnttype == '3') {
                    $paymenttype = 'JV';
                }

                // $q = SpecialExternalGcrequestItem::selectRaw("IFNULL(SUM(special_external_gcrequest_items.specit_qty),0) as cnt,
                //         IFNULL(SUM(special_external_gcrequest_items.specit_denoms * special_external_gcrequest_items.specit_qty),0) as totamt")
                //     ->where('specit_trid', $this->insp_trid)->first();
                // $totgccnt = $q->cnt;
                // $totdenom = $q->totamt;
            }
        }

        return [
            'inspPaymentnum' => $this->insp_paymentnum,
            'customer' => $customer,
            'date' => Date::parse($datetr)->toFormattedDateString(),
            'time' => Date::parse($datetr)->format('h:i A'),
            'totalAmount' => NumberHelper::currency($this->institut_amountrec),
            'payment' => $this->paymentType($paymenttype)

        ];
    }

    private function paymentType($payment)
    {
        $selec = [
            'cashcheck' => 'Check and Cash',
            'gad' => 'Giveaways and Donations'
        ];
        return $selec[$payment] ?? Str::ucfirst($payment);
    }
}
