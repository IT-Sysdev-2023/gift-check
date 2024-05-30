<?php

namespace App\Services\RetailStore;

use App\Models\CreditCard;
use App\Models\CreditcardPayment;
use App\Models\TransactionStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Sales
{

    private $request;
    public function __construct(Request $request) {
        $this->request = $request;
    }

    private function storeAssigned(){
        return $this->request->user()->store_assigned;
    }
    public function cashSales()
    {

        $record = TransactionStore::join('transaction_payment', 'transaction_stores.trans_sid', '=', 'transaction_payment.payment_trans_num')
            ->where([['trans_store', $this->storeAssigned()], ['transaction_stores.trans_type', 1]])
            ->select(
                'trans_sid',
                'trans_number',
                'trans_datetime',
                'transaction_payment.payment_docdisc',
                'transaction_payment.payment_linediscount',
                'transaction_payment.payment_amountdue',
                'transaction_payment.payment_stotal'
            )
            ->get();

        return $record;
    }
    public function cardSale()
    {

        $card = CreditCard::select('ccard_name', "ccard_id")->get();

        $card->each(function ($item, $key) {
            $db = CreditcardPayment::join('customer_internal_ar', 'cctrans_transid', '=', 'ar_trans_id')
                ->join('transaction_stores', 'cctrans_transid', '=', 'trans_sid')
                ->where([['cc_creaditcard', $item->ccard_id], ['ar_type', 2], ['trans_store', $this->storeAssigned()]])
                ->sum(DB::raw('IFNULL(customer_internal_ar.ar_dbamt, 0.00)'));
        });

        //     <?php 
        //     $where = 'creditcard_payment.cc_creaditcard ='.$c->ccard_id.' AND ar_type =2 AND 
        //       transaction_stores.trans_store='.$_SESSION['gc_store'];
        //     $select = 'IFNULL(SUM(customer_internal_ar.ar_dbamt),0.00) as totdb';
        //     $join = 'INNER JOIN
        //         customer_internal_ar
        //       ON
        //         creditcard_payment.cctrans_transid = customer_internal_ar.ar_trans_id
        //       INNER JOIN
        //         transaction_stores
        //       ON
        //         creditcard_payment.cctrans_transid = transaction_stores.trans_sid
        //       ';

        //     $db = getSelectedData($link,'creditcard_payment',$select,$where,$join,'');                      
        //     echo '&#8369 '.number_format($db->totdb,2);
        //     $gtotal += $db->totdb; 

        //         $select = "ccard_name, ccard_id";
//   $where ="1";
//   $cards = getAllData($link,'credit_cards',$select,$where,'','');

    }

    public function ArCustomer()
    {

        $record = TransactionStore::join('customer_internal_ar', 'trans_sid', '=', 'ar_trans_id')
            ->join('customer_internal', 'ar_cuscode', '=', 'ci_code')
            ->select(
                'trans_number',
                DB::raw('SUM(customer_internal_ar.ar_dbamt) as db'),
                DB::raw('SUM(customer_internal_ar.ar_cramt) as cr'),
                'ci_group',
                'ci_type',
                'ci_name',
                'ci_code'
            )
            ->where([['trans_type', '3'], ['ar_type', '1'], ['trans_store', $this->storeAssigned()]])
            ->orderBy('ar_cuscode')
            ->get()
            ->groupBy('ar_cuscode');

        return $record;

        // ->groupBy('ar_cuscode');
        //     $select = "transaction_stores.trans_number,
        //     SUM(customer_internal_ar.ar_dbamt) as db,
        //     SUM(customer_internal_ar.ar_cramt) as cr,
        //     customer_internal.ci_group,
        //     customer_internal.ci_type,
        //     customer_internal.ci_name,
        //     customer_internal.ci_code";
        //   $where =" transaction_stores.trans_type='3'
        //     AND
        //       customer_internal_ar.ar_type='1'
        //     AND
        //       transaction_stores.trans_store='".$_SESSION['gc_store']."'
        //     GROUP BY 
        //       customer_internal_ar.ar_cuscode 
        //     ORDER BY customer_internal_ar.ar_cuscode ASC";
        //   $join = 'INNER JOIN
        //       customer_internal_ar
        //     ON
        //       customer_internal_ar.ar_trans_id = transaction_stores.trans_sid
        //     INNER JOIN
        //       customer_internal
        //     ON
        //       customer_internal.ci_code=customer_internal_ar.ar_cuscode';

        //   $arlist = getAllData($link,'transaction_stores',$select,$where,$join,'');

        //   $group = array('','Head Office','Subs. Admin');
        //   $type = array('','Supplier','Customer','V.I.P.');

    }

    public function report()
    {
 //
    }
}